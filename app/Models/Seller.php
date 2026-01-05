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
        return $this->active_clients_count * 35.90 * 0.40;
    }

    /**
     * Get count of active clients.
     */
    public function getActiveClientsCountAttribute(): int
    {
        // Use loaded collection if available to ensure logic matches PHP time/attributes
        if ($this->relationLoaded('tenants')) {
            return $this->tenants->filter(function ($tenant) {
                // Ensure users are loaded or fetch them
                $users = $tenant->relationLoaded('users') ? $tenant->users : $tenant->users()->get();
                
                return $users->contains(function ($user) {
                    return $user->hasActiveSubscription() 
                        && !$user->isSuperAdmin()
                        && $user->subscription_type !== 'trial'; // No comisionar pruebas
                });
            })->count();
        }

        return $this->tenants()
            ->whereHas('users', function ($query) {
                $query->where('is_active', true)
                    ->where('email', '!=', 'amadocahuazavargas@gmail.com') // Exclude Super Admin
                    ->where('subscription_type', '!=', 'trial') // Exclude Trials
                    ->whereNotNull('subscription_expires_at')
                    ->where('subscription_expires_at', '>', Carbon::now());
            })->count();
    }
}
