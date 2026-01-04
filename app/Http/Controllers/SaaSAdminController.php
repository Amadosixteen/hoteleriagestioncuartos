<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SaaSAdminController extends Controller
{
    /**
     * Display a listing of all users and their subscription status.
     */
    public function index()
    {
        // Solo Amado puede entrar
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Acceso denegado. Solo el administrador global puede entrar aquí.');
        }

        $users = User::with('tenant')
            ->orderBy('subscription_expires_at', 'asc')
            ->get();

        return view('saas.admin', compact('users'));
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
            'is_active' => true
        ]);

        return back()->with('success', "Suscripción de {$user->name} renovada por 30 días adicionales.");
    }
}
