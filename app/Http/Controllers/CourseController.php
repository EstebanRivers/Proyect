<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CourseController extends Controller
{
    use AuthorizesRequests;

    /**
     * Mostrar lista de cursos
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obtener cursos activos ordenados por prerrequisitos
        $courses = Course::with(['instructor', 'prerequisites', 'enrollments'])
            ->where('status', 'activo')
            ->get()
            ->sortBy(function ($course) {
                return $course->prerequisites->count();
            });

        // Obtener cursos del usuario si es estudiante o anfitrión
        $userCourses = [];
        if ($user->hasAnyRole(['alumno', 'anfitrion'])) {
            $userCourses = $user->enrollments()
                ->with('course')
                ->get()
                ->keyBy('course_id');
        }

        return view('courses.index', compact('courses', 'userCourses'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $this->authorize('create', Course::class);
        
        $availableCourses = Course::where('status', 'activo')->get();
        $instructors = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['docente', 'admin']);
        })->get();

        return view('courses.create', compact('availableCourses', 'instructors'));
    }

    /**
     * Crear nuevo curso
     */
    public function store(Request $request)
    {
        $this->authorize('create', Course::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'code' => 'required|string|max:20|unique:courses,code',
            'credits' => 'required|integer|min:1|max:10',
            'duration_hours' => 'required|integer|min:1',
            'difficulty' => 'required|in:basico,intermedio,avanzado',
            'max_students' => 'required|integer|min:1|max:100',
            'min_students' => 'required|integer|min:1',
            'start_date' => 'nullable|date|after:today',
            'end_date' => 'nullable|date|after:start_date',
            'price' => 'required|numeric|min:0',
            'instructor_id' => 'required|exists:users,id',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'exists:courses,id',
        ]);

        try {
            DB::beginTransaction();

            $course = Course::create($validated);

            // Asignar prerrequisitos
            if ($request->has('prerequisites')) {
                $course->prerequisites()->attach($request->prerequisites);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Curso creado exitosamente.',
                    'redirect' => route('courses.index')
                ]);
            }

            return redirect()->route('courses.index')
                           ->with('success', 'Curso creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear el curso: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()
                        ->withErrors(['error' => 'Error al crear el curso.']);
        }
    }

    /**
     * Inscribirse a un curso
     */
    public function enroll(Course $course)
    {
        $user = Auth::user();

        // Verificar permisos
        if (!$user->hasAnyRole(['alumno', 'anfitrion'])) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para inscribirte a cursos.'
            ], 403);
        }

        // Verificar si puede inscribirse
        if (!$course->canUserEnroll($user)) {
            $missingPrereqs = $course->getMissingPrerequisites($user);
            
            if (!empty($missingPrereqs)) {
                $prereqNames = collect($missingPrereqs)->pluck('title')->join(', ');
                return response()->json([
                    'success' => false,
                    'message' => "Debes completar primero: {$prereqNames}"
                ], 400);
            }

            if ($course->is_full) {
                return response()->json([
                    'success' => false,
                    'message' => 'El curso ha alcanzado su capacidad máxima.'
                ], 400);
            }

            return response()->json([
                'success' => false,
                'message' => 'No puedes inscribirte a este curso en este momento.'
            ], 400);
        }

        try {
            CourseEnrollment::create([
                'course_id' => $course->id,
                'user_id' => $user->id,
                'status' => 'inscrito',
                'enrolled_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Te has inscrito exitosamente al curso.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al inscribirse al curso.'
            ], 500);
        }
    }

    /**
     * Desinscribirse de un curso
     */
    public function unenroll(Course $course)
    {
        $user = Auth::user();

        $enrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->where('status', 'inscrito')
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'No estás inscrito a este curso.'
            ], 400);
        }

        try {
            $enrollment->update(['status' => 'abandonado']);

            return response()->json([
                'success' => true,
                'message' => 'Te has desinscrito del curso.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al desinscribirse del curso.'
            ], 500);
        }
    }
}