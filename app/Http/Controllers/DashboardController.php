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
        $floors = Floor::with([
            'rooms.activeReservation.guests',
            'rooms.activeReservation' => function ($query) {
                $query->with('primaryGuest');
            }
        ])
        ->orderBy('floor_number')
        ->get();
        
        return view('dashboard', compact('floors'));
    }
}
