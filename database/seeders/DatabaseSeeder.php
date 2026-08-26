<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear Rol Admin
        $adminRole = Role::firstOrCreate(
            ['nombre' => 'Admin'],
            [
                'descripcion' => 'Administrador con acceso total al sistema.',
                'estado' => 'Activo',
                'nivel_acceso' => 10,
            ]
        );

        // Crear Usuario Admin
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@proyecto.com'],
            [
                'name' => 'Administrador Proyecto',
                'password' => Hash::make('admin12345'),
            ]
        );

        // Asignar Rol
        if (!$adminUser->roles()->where('role_id', $adminRole->id)->exists()) {
            $adminUser->roles()->attach($adminRole->id);
        }
    }
}
