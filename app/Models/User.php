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
     * Check if the user is a super admin.
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }
}
