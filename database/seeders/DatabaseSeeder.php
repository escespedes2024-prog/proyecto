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
        // Crear Roles estándar
        $roles = [
            'Administrador' => 'Administrador con acceso total al sistema.',
            'Secretario' => 'Gestiona personas y módulos operativos del sistema.',
            'Tesorero' => 'Gestiona cultos y las finanzas (ingresos, egresos, contratos).',
        ];

        $adminRole = null;
        $nivel = 0;
        foreach ($roles as $nombre => $descripcion) {
            $rol = Role::firstOrCreate(
                ['nombre' => $nombre],
                [
                    'descripcion' => $descripcion,
                    'estado' => 'Activo',
                    'nivel_acceso' => ++$nivel,
                ]
            );
            if ($nombre === 'Administrador') {
                $adminRole = $rol;
            }
        }

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
