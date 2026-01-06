<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Seller;
use App\Traits\HasTenantSetup;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SaaSAdminController extends Controller
{
    use HasTenantSetup;

    /**
     * Display a listing of all users and their subscription status.
     */
    public function index()
    {
        // Solo Amado puede entrar
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Acceso denegado. Solo el administrador global puede entrar aquí.');
        }

        $users = User::with('tenant.seller')
            ->orderBy('subscription_expires_at', 'asc')
            ->paginate(50);

        $sellers = Seller::with('tenants.users')->get();

        return view('saas.admin', compact('users', 'sellers'));
    }

    /**
     * Store a new hotel and user.
     */
    public function storeHotel(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $request->validate([
            'hotel_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:500',
            'email' => 'required|email|ends_with:@gmail.com|unique:users,email',
            'subscription_type' => 'required|in:trial,monthly',
            'seller_id' => 'nullable|exists:sellers,id',
        ]);

        // Crear Tenant
        $tenant = Tenant::create([
            'name' => $request->hotel_name . "'s Hotel",
            'phone' => $request->phone,
            'location' => $request->location,
            'slug' => Str::slug($request->email),
            'seller_id' => $request->seller_id,
            'registration_date' => Carbon::now(),
        ]);

        // Crear Estructura básica usando el Trait
        $this->setupTenantStructure($tenant);

        // Calcular expiración
        $expiresAt = $request->subscription_type === 'trial' 
            ? Carbon::now()->addDays(3) 
            : Carbon::now()->addDays(30);

        // Crear Usuario vinculado
        User::create([
            'name' => $request->hotel_name,
            'email' => $request->email,
            'password' => null, // Solo Google Auth
            'tenant_id' => $tenant->id,
            'is_active' => true,
            'is_admin' => true,
            'subscription_expires_at' => $expiresAt,
            'subscription_type' => $request->subscription_type,
        ]);

        return back()->with('success', "Hotel y usuario creados exitosamente con suscripción de " . ($request->subscription_type === 'trial' ? '3 días' : '30 días') . ".");
    }

    /**
     * Store a new seller.
     */
    public function storeSeller(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $request->validate([
            'dni' => 'required|string|unique:sellers,dni',
            'names' => 'required|string|max:255',
            'surnames' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|ends_with:@gmail.com',
        ]);

        $seller = Seller::create($request->only('dni', 'names', 'surnames', 'email'));

        // Crear usuario para el vendedor
        User::create([
            'name' => $seller->full_name,
            'email' => $request->email,
            'password' => null, // Google Auth
            'is_active' => true,
            'seller_id' => $seller->id,
            // No tiene tenant_id, ni suscripción (es staff)
        ]);

        return back()->with('success', "Vendedor registrado correctamente. Ahora puede iniciar sesión con su correo Google.");
    }

    /**
     * Renew a user's subscription for 30 days.
     */
    public function renew($id)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $user = User::findOrFail($id);
        
        // Si ya expiró, empezamos desde hoy. Si no, sumamos a lo que ya tenía.
        $startDate = $user->subscription_expires_at && $user->subscription_expires_at->isFuture() 
            ? $user->subscription_expires_at 
            : Carbon::now();

        $user->update([
            'subscription_expires_at' => $startDate->addDays(30),
            'subscription_type' => 'monthly', // Pasamos a mensual (pagado) tras renovar
            'is_active' => true
        ]);

        return back()->with('success', "Suscripción de {$user->name} renovada por 30 días adicionales.");
    }
}
