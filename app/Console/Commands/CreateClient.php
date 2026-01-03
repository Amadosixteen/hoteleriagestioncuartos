<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Tenant;
use App\Traits\HasTenantSetup;
use Illuminate\Support\Str;

class CreateClient extends Command
{
    use HasTenantSetup;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-client {name} {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea un nuevo cliente (tenant) manualmente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');

        if (User::where('email', $email)->exists()) {
            $this->error("El usuario con email {$email} ya existe.");
            return;
        }

        // Crear Tenant
        $tenant = Tenant::create([
            'name' => $name . "'s Hotel",
            'slug' => Str::slug($email),
        ]);

        // Crear Estructura básica usando el Trait
        $this->setupTenantStructure($tenant);

        // Crear Usuario vinculado
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => null, // Solo Google Auth
            'tenant_id' => $tenant->id,
            'is_active' => true,
        ]);

        $this->info("Cliente {$name} ({$email}) creado exitosamente.");
        $this->info("Ahora puede iniciar sesión con Google usando ese correo.");
    }
}
