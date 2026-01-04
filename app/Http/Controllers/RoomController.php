<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $floors = Floor::where('tenant_id', auth()->user()->tenant_id)
            ->with(['rooms' => function ($query) {
                $query->orderBy('position');
            }])
            ->orderBy('floor_number')
            ->get();
            
        return view('rooms.index', compact('floors'));
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'rooms' => 'required|array',
            'rooms.*.id' => 'required|exists:rooms,id',
            'rooms.*.floor_id' => 'required|exists:floors,id',
            'rooms.*.position' => 'required|integer',
        ]);

        foreach ($request->rooms as $roomData) {
            $room = Room::findOrFail($roomData['id']);
            
            // Security check: ensure the room belongs to a floor of the current tenant
            if ($room->floor->tenant_id !== auth()->user()->tenant_id) {
                continue;
            }

            $room->update([
                'floor_id' => $roomData['floor_id'],
                'position' => $roomData['position']
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'floor_id' => 'required|exists:floors,id',
            'room_number' => 'required|integer',
            'type' => 'required|in:Solo,Doble,Triple,Matrimonial,Familiar',
        ]);

        $floor = Floor::findOrFail($request->floor_id);

        // Security check
        if ($floor->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        // Check uniqueness in this floor
        $exists = Room::where('floor_id', $floor->id)
            ->where('room_number', $request->room_number)
            ->exists();

        if ($exists) {
            return back()->with('error', 'El número de habitación ya existe en este piso.')->withInput();
        }

        Room::create([
            'floor_id' => $floor->id,
            'room_number' => $request->room_number,
            'type' => $request->type,
            'status' => 'available',
        ]);

        return redirect()->route('rooms.index')->with('success', 'Habitación creada correctamente.');
    }

    public function destroy(Room $room)
    {
        // Security check through floor
        if ($room->floor->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        if ($room->status !== 'available' && $room->status !== 'cleaning') {
             return back()->with('error', 'No se puede eliminar una habitación ocupada o con reservación activa.');
        }

        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Habitación eliminada correctamente.');
    }
}
