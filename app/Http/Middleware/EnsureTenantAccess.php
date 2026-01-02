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

        return $next($request);
    }
}
