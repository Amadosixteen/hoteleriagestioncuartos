<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Floor;
use App\Models\Reservation;
use App\Models\Guest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        // 1. Tasa de ocupación por piso (Promedio actual)
        $occupancyByFloor = Floor::where('tenant_id', $tenantId)
            ->withCount(['rooms', 'rooms as occupied_rooms_count' => function ($query) {
                $query->whereIn('status', ['occupied', 'expired']);
            }])
            ->get()
            ->map(function ($floor) {
                $rate = $floor->rooms_count > 0 
                    ? ($floor->occupied_rooms_count / $floor->rooms_count) * 100 
                    : 0;
                return [
                    'name' => $floor->name,
                    'rate' => round($rate, 2)
                ];
            });

        // 2. Tendencia últimos 30 días
        $trend = Reservation::whereHas('room.floor', function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            })
            ->where('check_in_at', '>=', Carbon::now()->subDays(30))
            ->select(DB::raw('DATE(check_in_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 3. Clientes Frecuentes (Top 10)
        $frequentGuests = Guest::whereHas('reservation.room.floor', function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            })
            ->select('document_number', 'first_name', 'last_name', DB::raw('count(*) as count'))
            ->groupBy('document_number', 'first_name', 'last_name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // 4. Resumen general de ocupación
        $totalRooms = Room::whereHas('floor', function ($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })->count();
        
        $occupiedRooms = Room::whereHas('floor', function ($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })->whereIn('status', ['occupied', 'expired'])->count();

        $overallOccupancy = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;

        return view('analytics.index', compact(
            'occupancyByFloor', 
            'trend', 
            'frequentGuests', 
            'overallOccupancy',
            'totalRooms',
            'occupiedRooms'
        ));
    }
}
