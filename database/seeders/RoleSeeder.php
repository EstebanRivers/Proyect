<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'name' => 'docente',
                'display_name' => 'Profesor',
                'description' => 'Puede gestionar cursos y estudiantes'
            ],
            [
                'name' => 'alumno',
                'display_name' => 'Estudiante',
                'description' => 'Acceso a cursos asignados'
            ],
            [
                'name' => 'anfitrion',
                'display_name' => 'Anfitrion',
                'description' => 'Acceso al módulo de cursos'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}