<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use Illuminate\Http\Request;

class FloorController extends Controller
{
    public function index()
    {
        $floors = Floor::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('floor_number')
            ->get();
            
        return view('floors.index', compact('floors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'floor_number' => 'required|integer',
            'name' => 'required|string|max:255',
        ]);

        // Check uniqueness for this tenant
        $exists = Floor::where('tenant_id', auth()->user()->tenant_id)
            ->where('floor_number', $request->floor_number)
            ->exists();

        if ($exists) {
            return back()->with('error', 'El número de piso ya existe en este hotel.')->withInput();
        }

        Floor::create([
            'tenant_id' => auth()->user()->tenant_id,
            'floor_number' => $request->floor_number,
            'name' => $request->name,
        ]);

        return redirect()->route('floors.index')->with('success', 'Piso creado correctamente.');
    }

    public function destroy(Floor $floor)
    {
        // Security check
        if ($floor->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        if ($floor->rooms()->count() > 0) {
            return back()->with('error', 'No se puede eliminar un piso que tiene habitaciones.');
        }

        $floor->delete();

        return redirect()->route('floors.index')->with('success', 'Piso eliminado correctamente.');
    }
}
