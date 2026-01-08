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
    
    // Management Routes (General para dueños de hotel)
    Route::resource('floors', App\Http\Controllers\FloorController::class);
    Route::resource('rooms', App\Http\Controllers\RoomController::class);
    Route::get('/rates', [App\Http\Controllers\RateController::class, 'index'])->name('rates.index');
    Route::post('/rates/update', [App\Http\Controllers\RateController::class, 'update'])->name('rates.update');
    Route::post('/rooms/reorder', [App\Http\Controllers\RoomController::class, 'reorder'])->name('rooms.reorder');

    Route::get('/caja/report', [App\Http\Controllers\CajaController::class, 'report'])->name('caja.report');
    Route::get('/caja/data', [App\Http\Controllers\CajaController::class, 'data'])->name('caja.data');
    Route::post('/caja/upload-logo', [App\Http\Controllers\CajaController::class, 'uploadLogo'])->name('caja.upload-logo');
    
    Route::post('/reservations/{id}/apply-overtime', [App\Http\Controllers\ReservationController::class, 'applyOvertimeCharge'])->name('reservations.apply-overtime');
    
    Route::get('/settings', [App\Http\Controllers\TenantSettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/overtime-rate', [App\Http\Controllers\TenantSettingsController::class, 'updateOvertimeRate'])->name('settings.update-overtime-rate');

    Route::get('/analytics', [App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/calendar', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');

    // Route for Sellers
    Route::get('/seller/dashboard', [App\Http\Controllers\SellerController::class, 'index'])->name('seller.dashboard');

    // Management Routes (Solo Super Admin)
    Route::middleware(['superadmin'])->group(function () {
        Route::get('/hotels', [App\Http\Controllers\HotelController::class, 'index'])->name('hotels.index');
        Route::post('/hotels/{id}/switch', [App\Http\Controllers\HotelController::class, 'switch'])->name('hotels.switch');
        
        // Rutas de Administración SaaS
        Route::get('/saas-management', [App\Http\Controllers\SaaSAdminController::class, 'index'])->name('saas.admin');
        Route::post('/saas-management/renew/{id}', [App\Http\Controllers\SaaSAdminController::class, 'renew'])->name('saas.renew');
        Route::post('/saas-management/cancel-trial/{id}', [App\Http\Controllers\SaaSAdminController::class, 'cancelTrial'])->name('saas.cancel-trial');
        Route::post('/saas-management/hotel', [App\Http\Controllers\SaaSAdminController::class, 'storeHotel'])->name('saas.hotel.store');
        Route::post('/saas-management/seller', [App\Http\Controllers\SaaSAdminController::class, 'storeSeller'])->name('saas.seller.store');
    });
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
// Temporary Reset Route
Route::get('/saas/secret-db-reset', function () {
    if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
        abort(403);
    }
    
    try {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        // 1. Limpiar tablas transaccionales
        \App\Models\Reservation::truncate();
        \App\Models\Guest::truncate();
        
        // 2. Limpiar estructura de hoteles
        // Truncate es más rápido y resetea IDs, pero no hace cascada. Borramos explícitamente.
        \App\Models\Room::truncate();
        \App\Models\Floor::truncate();
        \App\Models\Tenant::truncate(); 
        
        // 3. Limpiar usuarios y vendedores
        \App\Models\Seller::truncate();
        \App\Models\User::where('email', '!=', 'amadocahuazavargas@gmail.com')->delete();
        
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
        
        return "ÉXITO: Base de datos de producción reiniciada. Se eliminaron hoteles, habitaciones, reservas y usuarios (excepto el Super Admin).";
    } catch (\Exception $e) {
        // En caso de error, reactivar checks y mostrar el mensaje
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
        return "ERROR: " . $e->getMessage() . " - FILE: " . $e->getFile() . " - LINE: " . $e->getLine();
    }
})->middleware(['auth', 'web']);
