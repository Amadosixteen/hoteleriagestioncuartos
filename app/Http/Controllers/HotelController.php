<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'No tienes permisos para ver todos los hoteles.');
        }

        $tenants = Tenant::all();
        return view('hotels.index', compact('tenants'));
    }

    public function switch($id)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'No tienes permisos para cambiar de hotel.');
        }

        $tenant = Tenant::findOrFail($id);
        
        $user = auth()->user();
        $user->tenant_id = $tenant->id;
        $user->save();

        return redirect()->route('dashboard')->with('success', "Cambiado al hotel: {$tenant->name}");
    }
}
