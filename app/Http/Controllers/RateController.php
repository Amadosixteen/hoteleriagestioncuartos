<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Floor;
use Illuminate\Http\Request;

class RateController extends Controller
{
    /**
     * Display the room rates dashboard.
     */
    public function index()
    {
        $floors = Floor::with(['rooms' => function($q) {
            $q->orderBy('position');
        }])->paginate(1); // Una página por piso para máxima claridad

        return view('rates.index', compact('floors'));
    }

    /**
     * Update room rates (individual or bulk).
     */
    public function update(Request $request)
    {
        $request->validate([
            'update_type' => 'required|in:individual,bulk_type,bulk_all',
            'room_id' => 'required_if:update_type,individual|exists:rooms,id',
            'type' => 'required_if:update_type,bulk_type|string',
            'price' => 'required|numeric|min:0',
        ]);

        $price = $request->price;

        if ($request->update_type === 'individual') {
            Room::where('id', $request->room_id)->update(['price' => $price]);
            $message = "Precio de la habitación actualizado a S/ " . number_format($price, 2);
        } elseif ($request->update_type === 'bulk_type') {
            Room::where('type', $request->type)->update(['price' => $price]);
            $message = "Todas las habitaciones de tipo '{$request->type}' actualizadas a S/ " . number_format($price, 2);
        } else {
            Room::query()->update(['price' => $price]);
            $message = "Todas las habitaciones del hotel actualizadas a S/ " . number_format($price, 2);
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}
