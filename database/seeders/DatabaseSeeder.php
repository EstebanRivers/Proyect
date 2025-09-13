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
            'carrera' => 'Licenciatura en Sistemas y Seguridad Informática',
            'matricula' => 'UHTA001',
            'semestre' => 8,
            'telefono' => '1234567896',
            'curp' => 'ABCD123456HDFGHI01',
            'fecha_nacimiento' => '2000-01-15',
            'edad' => 24,
            'colonia' => 'Centro',
            'calle' => 'Calle Principal #123',
            'ciudad' => 'Acapulco de Juárez',
            'estado' => 'Guerrero',
            'codigo_postal' => '39300',
        ]);

        // Asignar rol de admin al usuario de prueba
        $adminUser->assignRole('admin');

        // Crear usuarios adicionales con diferentes roles
        $teacher = User::factory()->create([
            'name' => 'Profesor Demo',
            'email' => 'profesor@example.com',
            'carrera' => 'Docente',
            'telefono' => '9876543210',
            'ciudad' => 'Acapulco de Juárez',
            'estado' => 'Guerrero',
        ]);
        $teacher->assignRole('teacher');

        $student = User::factory()->create([
            'name' => 'Estudiante Demo',
            'email' => 'estudiante@example.com',
            'carrera' => 'Licenciatura en Administración',
            'matricula' => 'UHTA002',
            'semestre' => 4,
            'telefono' => '5555555555',
            'ciudad' => 'Acapulco de Juárez',
            'estado' => 'Guerrero',
        ]);
        $student->assignRole('student');
    }
}
