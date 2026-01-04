<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservationController;

// Public routes
Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Protected routes (require authentication and tenant)
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservations/{id}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::put('/reservations/{id}', [ReservationController::class, 'update'])->name('reservations.update');
    Route::post('/reservations/{id}/checkout', [ReservationController::class, 'checkout'])->name('reservations.checkout');
    
    Route::get('/api/rooms/status', [ReservationController::class, 'getRoomStatus'])->name('api.rooms.status');
    Route::post('/rooms/{id}/toggle-cleaning', [ReservationController::class, 'toggleCleaning'])->name('rooms.toggle-cleaning');
    
    // Management Routes
    Route::get('/hotels', [App\Http\Controllers\HotelController::class, 'index'])->name('hotels.index');
    Route::post('/hotels/{id}/switch', [App\Http\Controllers\HotelController::class, 'switch'])->name('hotels.switch');
    
    Route::resource('floors', App\Http\Controllers\FloorController::class);
    Route::resource('rooms', App\Http\Controllers\RoomController::class);
    Route::post('/rooms/reorder', [App\Http\Controllers\RoomController::class, 'reorder'])->name('rooms.reorder');
    Route::put('/rooms/{room}', [App\Http\Controllers\RoomController::class, 'update'])->name('rooms.update');

    Route::get('/analytics', [App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/calendar', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');
    
    // Rutas de Administración SaaS (Solo Amado)
    Route::get('/saas-management', [App\Http\Controllers\SaaSAdminController::class, 'index'])->name('saas.admin');
    Route::post('/saas-management/renew/{id}', [App\Http\Controllers\SaaSAdminController::class, 'renew'])->name('saas.renew');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
