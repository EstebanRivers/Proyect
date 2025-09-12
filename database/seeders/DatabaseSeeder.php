<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);

        // User::factory(10)->create();

        $adminUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Asignar rol de admin al usuario de prueba
        $adminUser->assignRole('admin');

        // Crear usuarios adicionales con diferentes roles
        $teacher = User::factory()->create([
            'name' => 'Profesor Demo',
            'email' => 'profesor@example.com',
        ]);
        $teacher->assignRole('teacher');

        $student = User::factory()->create([
            'name' => 'Estudiante Demo',
            'email' => 'estudiante@example.com',
        ]);
        $student->assignRole('student');
    }
}
