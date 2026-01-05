<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Seller extends Model
{
    protected $fillable = [
        'dni',
        'names',
        'surnames',
    ];

    /**
     * Get the tenants for the seller.
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    /**
     * Get full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->names} {$this->surnames}";
    }

    /**
     * Calculate active commissions (40% of 35.90 per active hotel).
     */
    public function getActiveCommissionsAttribute(): float
    {
        $activeTenantsCount = $this->tenants()
            ->whereHas('users', function ($query) {
                $query->where('is_active', true)
                    ->where('subscription_expires_at', '>', Carbon::now());
            })->count();

        return $activeTenantsCount * 35.90 * 0.40;
    }

    /**
     * Get count of active clients.
     */
    public function getActiveClientsCountAttribute(): int
    {
        return $this->tenants()
            ->whereHas('users', function ($query) {
                $query->where('is_active', true)
                    ->where('subscription_expires_at', '>', Carbon::now());
            })->count();
    }
}
