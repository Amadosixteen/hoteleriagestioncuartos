<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->tenant_id) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'No tienes acceso a ningún hotel. Contacta al administrador.');
        }

        $user = auth()->user();

        // Inicializar suscripción si es nula (para usuarios antiguos post-migración)
        if (!$user->subscription_expires_at) {
            $user->update(['subscription_expires_at' => $user->created_at->addDays(30)]);
        }

        // Verificar expiración
        if ($user->subscription_expires_at->isPast()) {
            $user->update(['is_active' => false]);
            auth()->logout();
            return redirect()->route('login')->with('error', 'Tu suscripción ha expirado. Por favor, contacta al administrador para renovar (Yape: 905562625).');
        }

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Tu cuenta está desactivada.');
        }

        return $next($request);
    }
}
