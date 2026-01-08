<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        'google_id',
        'is_active',
        'is_admin',
        'subscription_expires_at',
        'subscription_type',
        'seller_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'subscription_expires_at' => 'datetime',
            'is_active' => 'boolean',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Get days remaining in subscription.
     */
    public function getDaysRemainingAttribute()
    {
        if (!$this->subscription_expires_at) return 0;
        
        $now = \Carbon\Carbon::now();
        $expires = \Carbon\Carbon::parse($this->subscription_expires_at);
        
        if ($now->greaterThan($expires)) return 0;
        
        // Usar diffInHours y redondear hacia arriba para evitar decimales extraños
        return (int) ceil($now->diffInHours($expires) / 24);
    }

    /**
     * Get hours remaining in subscription.
     */
    public function getHoursRemainingAttribute()
    {
        if (!$this->subscription_expires_at) return 0;
        
        $now = \Carbon\Carbon::now();
        $expires = \Carbon\Carbon::parse($this->subscription_expires_at);
        
        if ($now->greaterThan($expires)) return 0;
        
        return (int) ceil($now->diffInHours($expires));
    }

    /**
     * Get formatted time remaining display (shows hours if < 24hrs, otherwise days).
     */
    public function getTimeRemainingDisplayAttribute()
    {
        $hours = $this->hours_remaining;
        
        if ($hours < 24) {
            return $hours . ' hr' . ($hours != 1 ? 's' : '');
        }
        
        $days = $this->days_remaining;
        return $days . ' día' . ($days != 1 ? 's' : '');
    }

    /**
     * Check if the user has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        if ($this->isSuperAdmin()) return true;
        
        return $this->is_active && 
               $this->subscription_expires_at && 
               $this->subscription_expires_at->isFuture();
    }

    /**
     * Get the status label for the user.
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->isSuperAdmin()) {
            return 'SISTEMA';
        }

        if ($this->isSeller()) {
            return 'VENDEDOR';
        }

        if (!$this->is_active) {
            return 'Baneado';
        }

        if (!$this->hasActiveSubscription()) {
            return 'Vencido';
        }

        return 'Activo';
    }

    /**
     * Get subscription progress percentage (assuming 30 days cycle).
     */
    public function getSubscriptionProgressAttribute()
    {
        $days = $this->days_remaining;
        $percentage = ($days / 30) * 100;
        return min(100, max(0, $percentage));
    }

    /**
     * Get the tenant that owns the user.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the seller profile for this user (if they are a seller).
     */
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    /**
     * Check if the user is the SaaS super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->email === 'amadocahuazavargas@gmail.com';
    }

    /**
     * Check if the user is a hotel admin.
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * Check if the user is a seller.
     */
    public function isSeller(): bool
    {
        return !is_null($this->seller_id);
    }
}
