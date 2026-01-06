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
            // Caso especial: Super Admin viendo la ruta de vendedores.
            // Podríamos mostrar todos los tenants con vendedor?
            // Para cumplir estrictamente "casi la misma información", mostraremos todos los tenants asignados a algún vendedor?
            // O simplemente todos los tenants.
            $tenants = Tenant::with(['users', 'seller'])->whereNotNull('seller_id')->get();
            $sellerName = 'Super Admin (Vista Global)';
        } else {
            $tenants = $seller->tenants()->with('users')->get();
            $sellerName = $seller->full_name;
        }

        return view('sellers.dashboard', compact('tenants', 'sellerName'));
    }
}
