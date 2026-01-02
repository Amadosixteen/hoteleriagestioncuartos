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

class AuthController extends Controller
{
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
            
            // Find or create user
            $user = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();
            
            if ($user) {
                // Update existing user
                $user->update([
                    'google_id' => $googleUser->id,
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                ]);
            } else {
                // Create new user and tenant
                $tenant = Tenant::create([
                    'name' => $googleUser->name . "'s Hotel",
                    'slug' => Str::slug($googleUser->email),
                ]);
                
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'tenant_id' => $tenant->id,
                ]);
                
                // Create 3 floors with 10 rooms each
                $this->createFloorsAndRooms($tenant);
            }
            
            Auth::login($user);
            
            return redirect()->route('dashboard');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Error al autenticar con Google: ' . $e->getMessage());
        }
    }

    /**
     * Create floors and rooms for a new tenant.
     */
    private function createFloorsAndRooms(Tenant $tenant)
    {
        for ($floorNumber = 1; $floorNumber <= 3; $floorNumber++) {
            $floor = Floor::create([
                'tenant_id' => $tenant->id,
                'floor_number' => $floorNumber,
                'name' => "Piso {$floorNumber}",
            ]);
            
            // Create 10 rooms for this floor
            for ($roomNumber = 1; $roomNumber <= 10; $roomNumber++) {
                Room::create([
                    'floor_id' => $floor->id,
                    'room_number' => $roomNumber,
                    'status' => 'available',
                ]);
            }
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
