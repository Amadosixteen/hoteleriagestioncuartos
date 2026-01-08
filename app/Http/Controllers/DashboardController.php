<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with all floors and rooms.
     */
    public function index()
    {
        $floors = $this->getDashboardData();
        return view('dashboard', compact('floors'));
    }

    public function apiData()
    {
        $floors = $this->getDashboardData();
        return response()->json([
            'floors' => $floors,
            'overtime_rate' => auth()->user()->tenant->overtime_rate_per_hour ?? 0
        ]);
    }

    private function getDashboardData()
    {
        return Floor::where('tenant_id', auth()->user()->tenant_id)
            ->with([
                'rooms' => function ($query) {
                    $query->orderBy('position', 'asc');
                },
                'rooms.activeReservation.guests',
                'rooms.activeReservation' => function ($query) {
                    $query->with('primaryGuest');
                }
            ])
            ->orderBy('floor_number')
            ->get();
    }
}
