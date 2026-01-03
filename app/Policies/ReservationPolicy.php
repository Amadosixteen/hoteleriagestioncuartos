<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    /**
     * Determine whether the user can view the reservation.
     */
    public function view(User $user, Reservation $reservation): bool
    {
        return $reservation->room->floor->tenant_id === $user->tenant_id;
    }

    /**
     * Determine whether the user can update the reservation.
     */
    public function update(User $user, Reservation $reservation): bool
    {
        return $reservation->room->floor->tenant_id === $user->tenant_id;
    }

    /**
     * Determine whether the user can delete the reservation.
     */
    public function delete(User $user, Reservation $reservation): bool
    {
        return $reservation->room->floor->tenant_id === $user->tenant_id;
    }
}
