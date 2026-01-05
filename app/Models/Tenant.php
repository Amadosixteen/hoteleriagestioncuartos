<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'seller_id',
        'registration_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'registration_date' => 'datetime',
        ];
    }

    /**
     * Get the seller associated with the tenant.
     */
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    /**
     * Get the floors for the tenant.
     */
    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class);
    }

    /**
     * Get the users for the tenant.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
