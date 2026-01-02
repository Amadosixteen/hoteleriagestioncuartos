<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Reservation extends Model
{
    protected $fillable = [
        'room_id',
        'check_in_at',
        'check_out_at',
        'duration_hours',
        'has_vehicle',
        'status',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'has_vehicle' => 'boolean',
    ];

    /**
     * Get the room that owns the reservation.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the guests for the reservation.
     */
    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    /**
     * Get the primary guest.
     */
    public function primaryGuest()
    {
        return $this->hasOne(Guest::class)->where('is_primary', true);
    }

    /**
     * Get remaining time in seconds.
     */
    public function getRemainingTimeInSeconds(): int
    {
        $now = Carbon::now();
        $checkOut = Carbon::parse($this->check_out_at);
        
        if ($now->greaterThanOrEqualTo($checkOut)) {
            return 0;
        }
        
        return $now->diffInSeconds($checkOut);
    }

    /**
     * Get formatted remaining time.
     */
    public function getFormattedRemainingTime(): string
    {
        $seconds = $this->getRemainingTimeInSeconds();
        
        if ($seconds <= 0) {
            return 'Tiempo vencido';
        }
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        return sprintf('%dh %dm %ds', $hours, $minutes, $secs);
    }

    /**
     * Get progress percentage (0-100).
     */
    public function getProgressPercentage(): float
    {
        $totalSeconds = Carbon::parse($this->check_in_at)->diffInSeconds($this->check_out_at);
        $remainingSeconds = $this->getRemainingTimeInSeconds();
        
        if ($totalSeconds <= 0) {
            return 100;
        }
        
        $elapsed = $totalSeconds - $remainingSeconds;
        return min(100, max(0, ($elapsed / $totalSeconds) * 100));
    }

    /**
     * Update reservation and room status.
     */
    public function updateStatus(): void
    {
        if ($this->getRemainingTimeInSeconds() <= 0 && $this->status === 'active') {
            $this->status = 'expired';
            $this->save();
            
            $this->room->status = 'expired';
            $this->room->save();
        }
    }
}
