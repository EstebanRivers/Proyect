<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Role;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Obtener un instructor (docente o admin)
        $instructor = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['docente', 'admin']);
        })->first();

        if (!$instructor) {
            $this->command->warn('No hay instructores disponibles. Creando cursos sin instructor específico.');
            $instructor = User::first();
        }
                $instructor = User::whereHas('roles', function ($query) {
                    $query->whereIn('name', ['docente', 'admin']);
                })->first();

        if (!$instructor) {
            $this->command->warn('No hay instructores disponibles. Creando uno por defecto.');

            $instructor = User::create([
                'name' => 'Instructor Genérico',
                'email' => 'instructor@example.com',
                'password' => bcrypt('password'), // o el hash que uses
            ]);

            // Asignar rol por defecto
            $instructor->roles()->attach(
                Role::where('name', 'docente')->first()->id
            );
        }


        // Cursos básicos (sin prerrequisitos)
        $basicCourses = [
            [
                'title' => 'Introducción a la Computación',
                'code' => 'COMP101',
                'description' => 'Curso introductorio que cubre los conceptos fundamentales de la computación, historia de las computadoras, y conceptos básicos de programación.',
                'short_description' => 'Fundamentos básicos de computación y programación',
                'difficulty' => 'basico',
                'credits' => 3,
                'duration_hours' => 40,
                'max_students' => 30,
                'min_students' => 5,
                'instructor_id' => $instructor?->id,
                'status' => 'activo',
            ],
            [
                'title' => 'Matemáticas Básicas',
                'code' => 'MATH101',
                'description' => 'Repaso de conceptos matemáticos fundamentales necesarios para cursos de programación y ciencias de la computación.',
                'short_description' => 'Fundamentos matemáticos para programación',
                'difficulty' => 'basico',
                'credits' => 4,
                'duration_hours' => 50,
                'max_students' => 25,
                'min_students' => 8,
                'instructor_id' => $instructor?->id,
                'status' => 'activo',
            ],
            [
                'title' => 'Lógica de Programación',
                'code' => 'LOGIC101',
                'description' => 'Desarrollo del pensamiento lógico y algorítmico. Introducción a diagramas de flujo y pseudocódigo.',
                'short_description' => 'Desarrollo del pensamiento algorítmico',
                'difficulty' => 'basico',
                'credits' => 3,
                'duration_hours' => 35,
                'max_students' => 20,
                'min_students' => 6,
                'instructor_id' => $instructor?->id,
                'status' => 'activo',
            ],
        ];

        foreach ($basicCourses as $courseData) {
            Course::create($courseData);
        }

        // Cursos intermedios (con prerrequisitos básicos)
        $intermediateCourses = [
            [
                'title' => 'Programación en Python',
                'code' => 'PY201',
                'description' => 'Introducción a la programación usando Python. Sintaxis, estructuras de datos, funciones y programación orientada a objetos.',
                'short_description' => 'Aprende a programar con Python desde cero',
                'difficulty' => 'intermedio',
                'credits' => 4,
                'duration_hours' => 60,
                'max_students' => 25,
                'min_students' => 8,
                'instructor_id' => $instructor?->id,
                'status' => 'activo',
                'prerequisites' => ['COMP101', 'LOGIC101'],
            ],
            [
                'title' => 'Estructuras de Datos',
                'code' => 'DS201',
                'description' => 'Estudio de estructuras de datos fundamentales: arrays, listas, pilas, colas, árboles y grafos.',
                'short_description' => 'Estructuras de datos y algoritmos básicos',
                'difficulty' => 'intermedio',
                'credits' => 4,
                'duration_hours' => 55,
                'max_students' => 20,
                'min_students' => 6,
                'instructor_id' => $instructor?->id,
                'status' => 'activo',
                'prerequisites' => ['COMP101', 'MATH101'],
            ],
            [
                'title' => 'Desarrollo Web Frontend',
                'code' => 'WEB201',
                'description' => 'Introducción al desarrollo web frontend con HTML, CSS y JavaScript. Responsive design y frameworks modernos.',
                'short_description' => 'Crea sitios web modernos y responsivos',
                'difficulty' => 'intermedio',
                'credits' => 5,
                'duration_hours' => 70,
                'max_students' => 30,
                'min_students' => 10,
                'instructor_id' => $instructor?->id,
                'status' => 'activo',
                'prerequisites' => ['COMP101', 'LOGIC101'],
            ],
        ];

        foreach ($intermediateCourses as $courseData) {
            $prerequisites = $courseData['prerequisites'] ?? [];
            unset($courseData['prerequisites']);
            
            $course = Course::create($courseData);
            
            // Asignar prerrequisitos
            if (!empty($prerequisites)) {
                $prereqIds = Course::whereIn('code', $prerequisites)->pluck('id');
                $course->prerequisites()->attach($prereqIds);
            }
        }

        // Cursos avanzados (con prerrequisitos intermedios)
        $advancedCourses = [
            [
                'title' => 'Algoritmos Avanzados',
                'code' => 'ALG301',
                'description' => 'Algoritmos de ordenamiento, búsqueda, programación dinámica, algoritmos greedy y análisis de complejidad.',
                'short_description' => 'Algoritmos complejos y análisis de eficiencia',
                'difficulty' => 'avanzado',
                'credits' => 5,
                'duration_hours' => 80,
                'max_students' => 15,
                'min_students' => 5,
                'instructor_id' => $instructor?->id,
                'status' => 'activo',
                'prerequisites' => ['PY201', 'DS201'],
            ],
            [
                'title' => 'Desarrollo Full Stack',
                'code' => 'FS301',
                'description' => 'Desarrollo completo de aplicaciones web incluyendo backend, bases de datos, APIs y deployment.',
                'short_description' => 'Conviértete en desarrollador full stack',
                'difficulty' => 'avanzado',
                'credits' => 6,
                'duration_hours' => 100,
                'max_students' => 20,
                'min_students' => 8,
                'instructor_id' => $instructor?->id,
                'status' => 'activo',
                'prerequisites' => ['WEB201', 'PY201'],
            ],
            [
                'title' => 'Inteligencia Artificial',
                'code' => 'AI301',
                'description' => 'Introducción a la inteligencia artificial, machine learning, redes neuronales y aplicaciones prácticas.',
                'short_description' => 'Fundamentos de IA y machine learning',
                'difficulty' => 'avanzado',
                'credits' => 5,
                'duration_hours' => 90,
                'max_students' => 12,
                'min_students' => 4,
                'instructor_id' => $instructor?->id,
                'status' => 'activo',
                'prerequisites' => ['PY201', 'DS201', 'ALG301'],
            ],
        ];

        foreach ($advancedCourses as $courseData) {
            $prerequisites = $courseData['prerequisites'] ?? [];
            unset($courseData['prerequisites']);
            
            $course = Course::create($courseData);
            
            // Asignar prerrequisitos
            if (!empty($prerequisites)) {
                $prereqIds = Course::whereIn('code', $prerequisites)->pluck('id');
                $course->prerequisites()->attach($prereqIds);
            }
        }

        $this->command->info('Cursos de ejemplo creados exitosamente con sistema de prerrequisitos.');
    }
}