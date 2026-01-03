<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    /**
     * Determine whether the user can view the room.
     */
    public function view(User $user, Room $room): bool
    {
        return $room->floor->tenant_id === $user->tenant_id;
    }

    /**
     * Determine whether the user can update the room.
     */
    public function update(User $user, Room $room): bool
    {
        return $room->floor->tenant_id === $user->tenant_id;
    }
}
