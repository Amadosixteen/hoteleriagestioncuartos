<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use App\Models\Room;
use App\Policies\RoomPolicy;
use App\Models\Reservation;
use App\Policies\ReservationPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Gate::policy(Room::class, RoomPolicy::class);
        Gate::policy(Reservation::class, ReservationPolicy::class);
    }
}
