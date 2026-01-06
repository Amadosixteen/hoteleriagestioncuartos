<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class SellerController extends Controller
{
    /**
     * Display the seller dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // Verificar acceso: Vendedor o Super Admin
        if (!$user->isSeller() && !$user->isSuperAdmin()) {
            abort(403, 'Acceso denegado.');
        }

        // Si es Super Admin, mostrar dashboard genérico o redirigir (el usuario pidió ver lo mismo)
        // Pero el dashboard debe mostrar "sus clientes". Si es Super Admin, ¿qué clientes ve?
        // El usuario dijo: "yo tambien debo ver la ruta". Asumiremos que ve todo o se le pasa un seller_id?
        // "el vendedor solo puede ver su propia analitica, no de los demas vendedores"
        // Si el Super Admin entra, ¿ve las analíticas de quién? 
        // Probablemente el Super Admin quiera ver CÓMO ve un vendedor, pero sin seleccionar uno específico podría ser confuso.
        // O quizás el Super Admin ve una vista donde elige qué vendedor inspeccionar?
        // Simplificación: Si es Super Admin, redirigir a SaaS panel o mostrar vacío? 
        // El prompt dice: "yo tambien debo ver la ruta... implementado con el motivo de que ellos puedan ver..."
        // Si soy Super Admin, no tengo `seller_id`. 
        // Voy a hacer que si es Super Admin, muestre TODOS los datos (como un vendedor global) o simplemente aborte si no tiene seller_id asociado?
        // Mejor: Si es Super Admin, mostramos una vista "demo" o todos los tenants que tienen vendedor?
        // Vamos a asumir que si es SuperAdmin, ve TODO, o mostramos un mensaje.
        
        $seller = $user->seller;

        if ($user->isSuperAdmin()) {
            // 1. Clientes Directos (Total histórico)
            $tenants = Tenant::with(['users'])->whereNull('seller_id')->get();
            $totalClients = $tenants->count();

            // 2. Suscripciones Activas
            // A. Directas
            $activeDirectCount = User::where('is_active', true)
                ->whereHas('tenant', function($q) { 
                    $q->whereNull('seller_id'); 
                })->count();

            // B. De Vendedores
            $activeSellerCount = User::where('is_active', true)
                ->whereHas('tenant', function($q) { 
                    $q->whereNotNull('seller_id'); 
                })->count();

            $activeClients = $activeDirectCount + $activeSellerCount;

            // 3. Ganancia Mensual (Estimada)
            // - Directas: 100% de 35.90
            // - Vendedores: 60% de 35.90 (porque 40% es del vendedor)
            $directIncome = $activeDirectCount * 35.90;
            $sellerIncome = $activeSellerCount * (35.90 * 0.60);
            
            $monthlyCommission = $directIncome + $sellerIncome;
            
            // Pasar variables extra a la vista para mostrar detalles si es necesario
            // Usaremos una variable de sesión flash o view shares si la vista no soporta cambios de variable,
            // pero mejor pasamos todo en compact.
            // NOTA: La vista espera $activeClients para el KPI.
            // Para "mostrar directas y total", podriamos pasar un string o variables separadas.
            // Vamos a pasar variables separadas y actualizar la vista.
            
            $sellerName = 'Super Admin (Ganancias Globales)';
            
            return view('sellers.dashboard', compact('tenants', 'sellerName', 'totalClients', 'activeClients', 'monthlyCommission', 'activeDirectCount'));

        } else {
            $tenants = $seller->tenants()->with('users')->get();
            $sellerName = $seller->full_name;
            
            // Lógica original del modelo Seller (re-calculada aquí o usada de la vista si se pasaba implícitamente)
            // En el controlador original NO se pasaban $totalClients, etc. Se calculaban en la vista o modelo??
            // Espera, el controlador original solo pasaba $tenants y $sellerName.
            // La vista sellers.dashboard hacía cálculos con blade/php embebido? Vamos a revisar la vista.
            // SI, la vista original tenía lógica: 
            // $totalClients = $tenants->count(); 
            // $activeClients = $tenants->filter(...)->count();
            // $monthlyCommission = ...
            
            // Para mantener compatibilidad, si NO pasamos estas variables, la vista debería calcularlas como antes.
            // PERO si las pasamos, la vista debería usarlas.
            // Modificaremos la vista para usar lass variables si existen, o calcularlas si no.
            
            return view('sellers.dashboard', compact('tenants', 'sellerName'));
        }
    }
}
