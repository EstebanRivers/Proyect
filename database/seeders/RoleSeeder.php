<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrador',
                'description' => 'Acceso completo al sistema'
            ],
            [
                'name' => 'teacher',
                'display_name' => 'Profesor',
                'description' => 'Puede gestionar cursos y estudiantes'
            ],
            [
                'name' => 'student',
                'display_name' => 'Estudiante',
                'description' => 'Acceso a cursos asignados'
            ],
            [
                'name' => 'billing',
                'display_name' => 'Facturación',
                'description' => 'Acceso al módulo de facturación'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}