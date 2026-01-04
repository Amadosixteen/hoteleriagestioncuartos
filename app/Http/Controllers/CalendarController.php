<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        $date = $request->input('date', Carbon::today()->toDateString());
        $startDate = Carbon::parse($date)->startOfDay();
        $endDate = Carbon::parse($date)->endOfDay();

        // Obtener reservaciones que estuvieron activas en esa fecha
        $reservations = Reservation::whereHas('room.floor', function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            })
            ->with(['room.floor', 'guests'])
            ->where(function ($query) use ($startDate, $endDate) {
                // La reserva empezó antes que termine el día Y terminó después que empiece el día
                $query->where('check_in_at', '<=', $endDate)
                      ->where('check_out_at', '>=', $startDate);
            })
            ->get();

        return view('calendar.index', compact('reservations', 'date'));
    }
}
