<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    protected $fillable = [
        'floor_id',
        'room_number',
        'status',
    ];

    /**
     * Get the floor that owns the room.
     */
    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    /**
     * Get the active reservation for the room.
     */
    public function activeReservation(): HasOne
    {
        return $this->hasOne(Reservation::class)->where('status', 'active');
    }

    /**
     * Get all reservations for the room.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Check if the room is available.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Check if the room is occupied.
     */
    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }

    /**
     * Check if the room reservation is expired.
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }
}
