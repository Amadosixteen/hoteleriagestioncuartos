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
        'position',
        'status',
    ];

    /**
     * Map of statuses in English to Spanish.
     */
    const STATUS_MAP = [
        'available' => 'Disponible',
        'occupied' => 'Ocupado',
        'expired' => 'Ocupado (Vencido)',
        'cleaning' => 'Limpieza',
    ];

    /**
     * Get the status in Spanish.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_MAP[$this->status] ?? $this->status;
    }

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
        return $this->hasOne(Reservation::class)->whereIn('status', ['active', 'expired']);
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
        return $this->status === 'available' || $this->status === 'expired';
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
