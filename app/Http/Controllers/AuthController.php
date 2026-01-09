<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use App\Traits\HasTenantSetup;

class AuthController extends Controller
{
    use HasTenantSetup;

    /**
     * Redirect to Google OAuth.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Buscar usuario solo por email o google_id (no creamos si no existe)
            $user = User::where('email', $googleUser->email)
                ->orWhere('google_id', $googleUser->id)
                ->first();
            
            if (!$user) {
                return redirect()->route('login')
                    ->with('error', 'Su cuenta no está registrada en el sistema.')
                    ->with('unregistered', true);
            }

            if (!$user->is_active) {
                return redirect()->route('login')
                    ->with('error', 'Su suscripción ha expirado. Por favor, renueve para continuar.')
                    ->with('expired', true)
                    ->with('user_email', $user->email)
                    ->with('user_name', $user->name);
            }

            // Actualizar google_id si es necesario
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->id]);
            }
            
            Auth::login($user);
            
            if ($user->isSeller()) {
                return redirect()->route('seller.dashboard');
            }

            return redirect()->route('dashboard');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Error al autenticar con Google: ' . $e->getMessage());
        }
    }



    /**
     * Logout user.
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
