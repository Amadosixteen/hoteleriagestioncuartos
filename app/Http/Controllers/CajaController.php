<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CajaController extends Controller
{
    public function report(Request $request)
    {
        return view('caja.report');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $tenant = auth()->user()->tenant;
        if (!$tenant) {
            return response()->json(['error' => 'No tenant associated with user'], 403);
        }

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($tenant->logo) {
                Storage::disk('public')->delete($tenant->logo);
            }

            $path = $request->file('logo')->store('logos', 'public');
            $tenant->update(['logo' => $path]);

            return response()->json([
                'success' => true,
                'logo_url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    public function data(Request $request)
    {
        app()->setLocale('es');
        \Carbon\Carbon::setLocale('es');
        $tenantId = auth()->user()->tenant_id;
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));
        $date = $request->get('date'); // Format: YYYY-MM-DD for specific day

        $query = Reservation::query()
            ->where('tenant_id', $tenantId);

        if ($date) {
            $query->whereDate('check_in_at', $date);
            $periodLabel = Carbon::parse($date)->translatedFormat('d F Y');
        } else {
            $query->whereMonth('check_in_at', $month)
                  ->whereYear('check_in_at', $year);
            $periodLabel = Carbon::create($year, $month, 1)->translatedFormat('F Y');
        }

        $reservations = $query->get();

        // 1. Total Ventas
        $totalSales = $reservations->sum('total_price');

        // 2. Line Chart Data (4 sections if month, flat if day)
        $chartData = $this->getChartData($month, $year, $date, $tenantId);

        // 3. Top 5 Cuartos (Using the same filtered query, ordered by revenue)
        $topRooms = (clone $query)
            ->select('room_id', DB::raw('count(*) as total'), DB::raw('sum(total_price) as revenue'))
            ->groupBy('room_id')
            ->orderBy('revenue', 'desc')
            ->limit(5)
            ->with('room')
            ->get();

        // 4. Ventas por categoría
        $categories = ['Solo', 'Doble', 'Triple', 'Matrimonial', 'Familiar'];
        $salesByCategory = [];
        $totalCount = $reservations->count();

        foreach ($categories as $cat) {
            $count = $reservations->where('room.type', $cat)->count();
            $percentage = $totalCount > 0 ? round(($count / $totalCount) * 100) : 0;
            $salesByCategory[] = [
                'name' => $cat,
                'percentage' => $percentage
            ];
        }

        $tenant = auth()->user()->tenant;
        $logoUrl = $tenant && $tenant->logo ? asset('storage/' . $tenant->logo) : null;

        return response()->json([
            'total_sales' => $totalSales,
            'period_label' => strtoupper($periodLabel),
            'chart_data' => $chartData,
            'top_rooms' => $topRooms->map(function($tr) {
                return [
                    'number' => $tr->room->room_number,
                    'price' => $tr->revenue, // Accumulated revenue for the room
                ];
            }),
            'sales_by_category' => $salesByCategory,
            'reservations_count' => $totalCount,
            'hotel_logo' => $logoUrl
        ]);
    }

    private function getChartData($month, $year, $date, $tenantId)
    {
        if ($date) {
            // If it's a specific day, show the actual day number as label
            $dayLabel = Carbon::parse($date)->format('d');
            return [
                'labels' => [$dayLabel],
                'values' => [Reservation::whereDate('check_in_at', $date)->where('tenant_id', $tenantId)->sum('total_price')]
            ];
        }

        // Get the actual number of days in the month
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        
        // Create dynamic week ranges (approximately 7 days each)
        $ranges = [
            [1, 7],
            [8, 14],
            [15, 21],
            [22, $daysInMonth]
        ];

        $labels = [];
        $values = [];
        
        // Get month abbreviation in Spanish
        $monthName = Carbon::create($year, $month, 1)->translatedFormat('M');
        
        foreach ($ranges as $range) {
            // Create label like "1-7 Ene", "8-14 Ene", etc.
            $labels[] = $range[0] . '-' . $range[1] . ' ' . $monthName;
            
            // Calculate sales for this range
            $val = Reservation::query()
                ->where('tenant_id', $tenantId)
                ->whereYear('check_in_at', $year)
                ->whereMonth('check_in_at', $month)
                ->whereBetween(DB::raw('DAY(check_in_at)'), [$range[0], $range[1]])
                ->sum('total_price');
            $values[] = $val;
        }

        return [
            'labels' => $labels,
            'values' => $values
        ];
    }
}
