<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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

        $adminRole = 
        Role::where('name', 'admin')->first();

        if ($adminRole){ User::create([
            'name' => 'Master',
            'email' => 'admin@uhta.com',
            'password'=>Hash::make('admin123'),
            'carrera' => 'N/A',
            'matricula' => 'UHTA001',
            'semestre' => '10',
            'telefono' => '1234567896',
            'curp' => 'ABCD123456HDFGHI01',
            'fecha_nacimiento' => '2000-01-15',
            'edad' => 24,
            'colonia' => 'Centro',
            'calle' => 'Calle Principal #123',
            'ciudad' => 'Acapulco de Juárez',
            'estado' => 'Guerrero',
            'codigo_postal' => '39300',
        ])->roles()->attach($adminRole);
    }

        // Crear usuarios adicionales con diferentes roles
        $teacher = User::factory()->create([
            'name' => 'Profesor Demo',
            'email' => 'profesor@example.com',
            'carrera' => 'Docente',
            'telefono' => '9876543210',
            'ciudad' => 'Acapulco de Juárez',
            'estado' => 'Guerrero',
        ]);
        $teacher->assignRole('docente');

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
        $student->assignRole('alumno');
    }
}
