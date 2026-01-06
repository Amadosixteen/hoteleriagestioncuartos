<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    public function report(Request $request)
    {
        return view('caja.report');
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
            ->whereHas('room.floor', function($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            });

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

        // 3. Top 5 Cuartos
        $topRooms = Reservation::query()
            ->select('room_id', DB::raw('count(*) as total'), DB::raw('sum(total_price) as revenue'))
            ->whereHas('room.floor', function($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
            })
            ->whereMonth('check_in_at', $month)
            ->whereYear('check_in_at', $year)
            ->groupBy('room_id')
            ->orderBy('total', 'desc')
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

        return response()->json([
            'total_sales' => number_format($totalSales, 2, '.', ','),
            'period_label' => strtoupper($periodLabel),
            'chart_data' => $chartData,
            'top_rooms' => $topRooms->map(function($tr) {
                return [
                    'number' => $tr->room->room_number,
                    'price' => number_format($tr->revenue / $tr->total, 2, '.', ','), // Average price sold
                ];
            }),
            'sales_by_category' => $salesByCategory,
            'reservations_count' => $totalCount
        ]);
    }

    private function getChartData($month, $year, $date, $tenantId)
    {
        if ($date) {
            // If it's a specific day, maybe show by hours or just a single point
            // For now, let's keep it simple as requested: image shows sem1-sem4 for month
            return [
                'labels' => ['Hoy'],
                'values' => [Reservation::whereDate('check_in_at', $date)->sum('total_price')]
            ];
        }

        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $ranges = [
            [1, 7],
            [8, 14],
            [15, 21],
            [22, $daysInMonth]
        ];

        $values = [];
        foreach ($ranges as $range) {
            $val = Reservation::query()
                ->whereHas('room.floor', function($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId);
                })
                ->whereYear('check_in_at', $year)
                ->whereMonth('check_in_at', $month)
                ->whereBetween(DB::raw('DAY(check_in_at)'), [$range[0], $range[1]])
                ->sum('total_price');
            $values[] = $val;
        }

        return [
            'labels' => ['sem1', 'sem2', 'sem3', 'sem4'],
            'values' => $values
        ];
    }
}
