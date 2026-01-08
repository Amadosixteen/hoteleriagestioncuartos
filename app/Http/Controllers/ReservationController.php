<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Reservation;
use App\Models\Guest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    /**
     * Store a new reservation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'duration_hours' => 'required|integer|min:1',
            'guests' => 'required|array|min:1',
            'guests.*.document_type' => 'required|in:dni,carnet_extranjeria,otro',
            'guests.*.custom_document_type' => 'nullable|string',
            'guests.*.document_number' => 'required|string',
            'guests.*.first_name' => 'required|string',
            'guests.*.last_name' => 'required|string',
            'guests.*.gender' => 'nullable|in:masculino,femenino,otro',
            'guests.*.vehicle_plate' => 'nullable|string',
        ]);

        $room = Room::findOrFail($validated['room_id']);
        $this->authorize('update', $room);
        
        // Check if room is available
        if (!$room->isAvailable()) {
            return back()->with('error', 'El cuarto no está disponible');
        }

        $durationHours = (int) $validated['duration_hours'];
        $checkInAt = Carbon::now();
        $checkOutAt = $checkInAt->copy()->addHours($durationHours);

        // Check if any guest has a vehicle
        $hasVehicle = collect($validated['guests'])->contains(function ($guest) {
            return !empty($guest['vehicle_plate']);
        });

        return DB::transaction(function () use ($validated, $room, $checkInAt, $checkOutAt, $hasVehicle) {
            // If room was expired, complete the previous reservation
            if ($room->status === 'expired' && $room->activeReservation) {
                $room->activeReservation->update(['status' => 'completed']);
            }

            // Create reservation
            $reservation = Reservation::create([
                'room_id' => $room->id,
                'check_in_at' => $checkInAt,
                'check_out_at' => $checkOutAt,
                'duration_hours' => $validated['duration_hours'],
                'has_vehicle' => $hasVehicle,
                'price' => $room->price,
                'total_price' => $room->price,
                'status' => 'active',
                'tenant_id' => $room->floor->tenant_id,
            ]);

            // Create guests
            foreach ($validated['guests'] as $index => $guestData) {
                Guest::create([
                    'reservation_id' => $reservation->id,
                    'document_type' => $guestData['document_type'],
                    'custom_document_type' => $guestData['custom_document_type'] ?? null,
                    'document_number' => $guestData['document_number'],
                    'first_name' => $guestData['first_name'],
                    'last_name' => $guestData['last_name'],
                    'gender' => $guestData['gender'] ?? null,
                    'vehicle_plate' => $guestData['vehicle_plate'] ?? null,
                    'is_primary' => $index === 0,
                ]);
            }

            // Update room status
            $room->update(['status' => 'occupied']);

            if (request()->wantsJson()) {
                return response()->json(['message' => 'Reserva creada exitosamente']);
            }

            return redirect()->route('dashboard')->with('success', 'Reserva creada exitosamente');
        });
    }

    /**
     * Get reservation details.
     */
    public function show($id)
    {
        $reservation = Reservation::with('guests')->findOrFail($id);
        $this->authorize('view', $reservation);
        return response()->json($reservation);
    }

    /**
     * Update reservation.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'duration_hours' => 'sometimes|integer|min:1',
            'guests' => 'sometimes|array|min:1',
            'guests.*.id' => 'nullable',
            'guests.*.document_type' => 'required|in:dni,carnet_extranjeria,otro',
            'guests.*.custom_document_type' => 'nullable|string',
            'guests.*.document_number' => 'required|string',
            'guests.*.first_name' => 'required|string',
            'guests.*.last_name' => 'required|string',
            'guests.*.gender' => 'nullable|in:masculino,femenino,otro',
            'guests.*.vehicle_plate' => 'nullable|string',
        ]);

        $reservation = Reservation::findOrFail($id);
        $this->authorize('update', $reservation);

        return DB::transaction(function () use ($validated, $reservation) {
            // Update duration if provided
            if (isset($validated['duration_hours'])) {
                $durationHours = (int) $validated['duration_hours'];
                $checkOutAt = Carbon::parse($reservation->check_in_at)->addHours($durationHours);
                $reservation->update([
                    'check_out_at' => $checkOutAt,
                    'duration_hours' => $durationHours,
                    'total_price' => $reservation->price,
                ]);
            }

            // Update guests if provided
            if (isset($validated['guests'])) {
                // Check if any guest has a vehicle
                $hasVehicle = collect($validated['guests'])->contains(function ($guest) {
                    return !empty($guest['vehicle_plate']);
                });
                
                $reservation->update(['has_vehicle' => $hasVehicle]);

                // Delete existing guests and create new ones
                $reservation->guests()->delete();
                
                foreach ($validated['guests'] as $index => $guestData) {
                    Guest::create([
                        'reservation_id' => $reservation->id,
                        'document_type' => $guestData['document_type'],
                        'custom_document_type' => $guestData['custom_document_type'] ?? null,
                        'document_number' => $guestData['document_number'],
                        'first_name' => $guestData['first_name'],
                        'last_name' => $guestData['last_name'],
                        'gender' => $guestData['gender'] ?? null,
                        'vehicle_plate' => $guestData['vehicle_plate'] ?? null,
                        'is_primary' => $index === 0,
                    ]);
                }
            }

            if (request()->wantsJson()) {
                return response()->json(['message' => 'Reserva actualizada exitosamente']);
            }

            return redirect()->route('dashboard')->with('success', 'Reserva actualizada exitosamente');
        });
    }

    /**
     * Checkout (complete reservation).
     */
    public function checkout($id)
    {
        $reservation = Reservation::findOrFail($id);
        $this->authorize('update', $reservation);

        return DB::transaction(function () use ($reservation) {
            $reservation->update([
                'status' => 'completed',
                'check_out_at' => \Carbon\Carbon::now()
            ]);
            $reservation->room->update(['status' => 'available']);

            if (request()->wantsJson()) {
                return response()->json(['message' => 'Check-out realizado exitosamente']);
            }

            return redirect()->route('dashboard')->with('success', 'Check-out realizado exitosamente');
        });
    }

    /**
     * Get room status (for AJAX updates).
     */
    public function getRoomStatus()
    {
        $rooms = Room::with(['activeReservation.primaryGuest'])
            ->whereHas('floor', function ($query) {
                $query->where('tenant_id', auth()->user()->tenant_id);
            })
            ->orderBy('position')
            ->get();

        $roomsData = $rooms->map(function ($room) {
            $data = [
                'id' => $room->id,
                'status' => $room->status,
            ];

            if ($room->activeReservation) {
                $reservation = $room->activeReservation;
                $reservation->updateStatus();
                
                $data['reservation'] = [
                    'id' => $reservation->id,
                    'remaining_time' => $reservation->getFormattedRemainingTime(),
                    'progress' => $reservation->getProgressPercentage(),
                    'guest_name' => $reservation->primaryGuest ? $reservation->primaryGuest->full_name : 'N/A',
                    'has_vehicle' => $reservation->has_vehicle,
                    'status' => $reservation->status,
                ];
                
                // Update room status if reservation expired
                if ($reservation->status === 'expired') {
                    $room->update(['status' => 'expired']);
                    $data['status'] = 'expired';
                }
            }

            return $data;
        });

        return response()->json($roomsData);
    }

    /**
     * Toggle cleaning status for a room.
     */
    public function toggleCleaning($id)
    {
        $room = Room::findOrFail($id);
        $this->authorize('update', $room);
        
        if ($room->status === 'available' || $room->status === 'expired') {
            // Complete previous reservation if it exists (e.g. if expired)
            if ($room->activeReservation) {
                $room->activeReservation->update([
                    'status' => 'completed',
                    'check_out_at' => \Carbon\Carbon::now()
                ]);
            }

            $room->update(['status' => 'cleaning']);
            $message = 'Cuarto puesto en limpieza';
        } elseif ($room->status === 'cleaning') {
            $room->update(['status' => 'available']);
            $message = 'Limpieza terminada. Cuarto disponible.';
        } else {
            return response()->json(['message' => 'El cuarto está ocupado y no puede entrar en limpieza ahora.'], 422);
        }

        return response()->json(['message' => $message]);
    }

    /**
     * Apply overtime charge to an expired reservation.
     */
    public function applyOvertimeCharge(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        $this->authorize('update', $reservation->room);

        // Verify reservation is active
        // We removed the strict time check (check_out_at > now()) to allow the admin
        // to manually apply overtime charges even if the server time is slightly behind
        // or if there are timezone discrepancies. We trust the admin's manual action.
        if ($reservation->status !== 'active') {
            return response()->json(['error' => 'La reserva no está activa'], 422);
        }

        // Calculate overtime hours
        $checkoutAt = $reservation->check_out_at;
        $now = now();
        $overtimeMinutes = $checkoutAt->diffInMinutes($now);
        $overtimeHours = round($overtimeMinutes / 60, 2);

        // Use custom charge if provided, otherwise calculate
        if ($request->has('custom_charge')) {
            $overtimeCharge = round($request->input('custom_charge'), 2);
        } else {
            // Get tenant's overtime rate
            $tenant = auth()->user()->tenant;
            $overtimeRate = $tenant->overtime_rate_per_hour ?? 0;

            if ($overtimeRate <= 0) {
                return response()->json(['error' => 'No se ha configurado una tarifa de tiempo extra'], 422);
            }

            // Calculate charge
            $overtimeCharge = round($overtimeHours * $overtimeRate, 2);
        }

        // Update reservation
        $reservation->update([
            'overtime_hours' => $overtimeHours,
            'overtime_charge' => $overtimeCharge,
            'total_price' => $reservation->total_price + $overtimeCharge
        ]);

        return response()->json([
            'success' => true,
            'overtime_hours' => $overtimeHours,
            'overtime_charge' => $overtimeCharge,
            'new_total' => $reservation->total_price
        ]);
    }
}
