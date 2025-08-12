<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Crear el usuario administrador
     * 
     * Como no manejamos roles, simplemente creamos un usuario
     * que será el administrador por defecto
     */
    public function run(): void
    {
        // Verificar si ya existe el admin por email
        $existingAdmin = User::where('email', 'admin@tienda.com')->first();
        
        if ($existingAdmin) {
            $this->command->info('❗ Ya existe el usuario administrador');
            return;
        }

        // Crear el usuario administrador
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@tienda.com',
            'password' => Hash::make('admin123456'),
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Usuario administrador creado:');
        $this->command->line('📧 Email: admin@tienda.com');
        $this->command->line('🔑 Contraseña: admin123456');
        $this->command->warn('🔒 Cambia la contraseña después del primer login');
    }
}