<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TenantSettingsController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;
        return view('settings.index', compact('tenant'));
    }

    public function updateOvertimeRate(Request $request)
    {
        $validated = $request->validate([
            'overtime_rate_per_hour' => 'required|numeric|min:0|max:9999.99'
        ]);

        $tenant = auth()->user()->tenant;
        $tenant->update([
            'overtime_rate_per_hour' => $validated['overtime_rate_per_hour']
        ]);

        return back()->with('success', 'Tarifa de tiempo extra actualizada correctamente');
    }
}
