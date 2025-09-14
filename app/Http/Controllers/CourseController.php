<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use App\Models\CourseTopic;
use App\Models\StudentTopicProgress;
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
            'credits' => 'required|integer|min:1|max:10',
            'duration_hours' => 'required|integer|min:1',
            'difficulty' => 'required|in:basico,intermedio,avanzado',
            'max_students' => 'required|integer|min:1|max:100',
            'min_students' => 'required|integer|min:1',
            'start_date' => 'nullable|date|after:today',
            'end_date' => 'nullable|date|after:start_date',
            'instructor_id' => 'required|exists:users,id',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'exists:courses,id',
            'topics' => 'required|array|min:1',
            'topics.*.title' => 'required|string|max:255',
            'topics.*.description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $course = Course::create($validated);

            // Asignar prerrequisitos
            if ($request->has('prerequisites')) {
                $course->prerequisites()->attach($request->prerequisites);
            }

            // Crear temas del curso
            if ($request->has('topics')) {
                foreach ($request->topics as $index => $topicData) {
                    $topic = $course->topics()->create([
                        'title' => $topicData['title'],
                        'description' => $topicData['description'] ?? null,
                        'order' => $index + 1,
                    ]);
                    
                    // Crear contenidos del tema
                    $this->createTopicContents($topic, $topicData);
                    
                    // Crear actividades del tema
                    $this->createTopicActivities($topic, $topicData);
                }
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
     * Mostrar un curso específico
     */
    public function show(Course $course, Request $request)
    {
        $user = Auth::user();
        
        // Cargar relaciones necesarias
        $course->load(['instructor', 'prerequisites', 'topics.contents', 'topics.activities']);
        
        // Verificar inscripción del usuario
        $enrollment = null;
        if ($user->hasAnyRole(['alumno', 'anfitrion'])) {
            $enrollment = CourseEnrollment::where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->first();
        }
        
        // Calcular progreso si está inscrito
        $completedTopics = 0;
        $currentTopic = null;
        
        if ($enrollment && $enrollment->status === 'inscrito') {
            $completedTopics = $course->topics->filter(function ($topic) use ($user) {
                return $topic->isCompletedByUser($user);
            })->count();
            
            // Obtener tema actual si se especifica
            if ($request->has('topic')) {
                $currentTopic = $course->topics->find($request->get('topic'));
            }
        }
        
        return view('courses.show', compact('course', 'enrollment', 'completedTopics', 'currentTopic'));
    }
    
    /**
     * Mostrar contenido de un tema específico (AJAX)
     */
    public function showTopic(Course $course, CourseTopic $topic)
    {
        $user = Auth::user();
        
        // Verificar que el usuario esté inscrito
        $enrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->where('status', 'inscrito')
            ->first();
            
        if (!$enrollment) {
            return response()->json(['error' => 'No estás inscrito en este curso'], 403);
        }
        
        // Verificar que el tema pertenezca al curso
        if ($topic->course_id !== $course->id) {
            return response()->json(['error' => 'Tema no encontrado'], 404);
        }
        
        // Cargar contenidos y actividades
        $topic->load(['contents', 'activities']);
        
        return view('courses.partials.topic-content', compact('topic'))->render();
    }
    
    /**
     * Marcar tema como completado
     */
    public function completeTopic(CourseTopic $topic)
    {
        $user = Auth::user();
        
        // Verificar que el usuario esté inscrito en el curso
        $enrollment = CourseEnrollment::where('course_id', $topic->course_id)
            ->where('user_id', $user->id)
            ->where('status', 'inscrito')
            ->first();
            
        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'No estás inscrito en este curso.'
            ], 403);
        }
        
        try {
            // Crear o actualizar progreso del tema
            $progress = StudentTopicProgress::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'topic_id' => $topic->id,
                ],
                [
                    'content_completed' => true,
                    'activities_completed' => true, // Por ahora marcamos como completado
                    'topic_score' => 100, // Puntuación por defecto
                    'completed_at' => now(),
                ]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Tema marcado como completado exitosamente.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar el tema como completado.'
            ], 500);
        }
    }
    
    /**
     * Crear contenidos para un tema
     */
    private function createTopicContents(CourseTopic $topic, array $topicData)
    {
        $order = 1;
        
        // Videos
        $videosCount = intval($topicData['videos_count'] ?? 0);
        for ($i = 1; $i <= $videosCount; $i++) {
            $topic->contents()->create([
                'title' => "Video {$i} - {$topic->title}",
                'description' => 'Video educativo del tema',
                'type' => 'video',
                'order' => $order++,
                'duration_minutes' => 15, // Duración por defecto
            ]);
        }
        
        // Documentos
        $documentsCount = intval($topicData['documents_count'] ?? 0);
        for ($i = 1; $i <= $documentsCount; $i++) {
            $topic->contents()->create([
                'title' => "Documento {$i} - {$topic->title}",
                'description' => 'Material de lectura del tema',
                'type' => 'document',
                'order' => $order++,
                'duration_minutes' => 10,
            ]);
        }
        
        // Presentaciones
        $presentationsCount = intval($topicData['presentations_count'] ?? 0);
        for ($i = 1; $i <= $presentationsCount; $i++) {
            $topic->contents()->create([
                'title' => "Presentación {$i} - {$topic->title}",
                'description' => 'Presentación del tema',
                'type' => 'presentation',
                'order' => $order++,
                'duration_minutes' => 20,
            ]);
        }
        
        // Contenido de texto
        if (intval($topicData['has_text_content'] ?? 0) === 1) {
            $topic->contents()->create([
                'title' => "Contenido - {$topic->title}",
                'description' => 'Contenido textual del tema',
                'type' => 'text',
                'content' => 'Contenido del tema que será editado por el instructor.',
                'order' => $order++,
                'duration_minutes' => 5,
            ]);
        }
    }
    
    /**
     * Crear actividades para un tema
     */
    private function createTopicActivities(CourseTopic $topic, array $topicData)
    {
        // Cuestionarios de opción múltiple
        $quizMultipleCount = intval($topicData['quiz_multiple_count'] ?? 0);
        for ($i = 1; $i <= $quizMultipleCount; $i++) {
            $topic->activities()->create([
                'title' => "Cuestionario {$i} - {$topic->title}",
                'description' => 'Cuestionario de opción múltiple',
                'type' => 'quiz_multiple',
                'content' => [
                    'questions' => [
                        [
                            'question' => 'Pregunta de ejemplo',
                            'options' => ['Opción A', 'Opción B', 'Opción C', 'Opción D'],
                            'correct_answer' => 0
                        ]
                    ]
                ],
                'max_attempts' => 3,
                'time_limit_minutes' => 30,
                'max_score' => 100,
            ]);
        }
        
        // Cuestionarios abiertos
        $quizOpenCount = intval($topicData['quiz_open_count'] ?? 0);
        for ($i = 1; $i <= $quizOpenCount; $i++) {
            $topic->activities()->create([
                'title' => "Cuestionario Abierto {$i} - {$topic->title}",
                'description' => 'Cuestionario de respuesta abierta',
                'type' => 'quiz_open',
                'content' => [
                    'questions' => [
                        [
                            'question' => 'Pregunta abierta de ejemplo',
                            'max_words' => 200
                        ]
                    ]
                ],
                'max_attempts' => 2,
                'max_score' => 100,
            ]);
        }
        
        // Ensayos
        $essayCount = intval($topicData['essay_count'] ?? 0);
        for ($i = 1; $i <= $essayCount; $i++) {
            $topic->activities()->create([
                'title' => "Ensayo {$i} - {$topic->title}",
                'description' => 'Ensayo sobre el tema',
                'type' => 'essay',
                'content' => [
                    'prompt' => 'Escribe un ensayo sobre los conceptos aprendidos en este tema.',
                    'min_words' => 500,
                    'max_words' => 1500,
                    'rubric' => [
                        'content' => 40,
                        'organization' => 30,
                        'grammar' => 30
                    ]
                ],
                'max_attempts' => 1,
                'max_score' => 100,
            ]);
        }
        
        // Tareas/Asignaciones
        $assignmentCount = intval($topicData['assignment_count'] ?? 0);
        for ($i = 1; $i <= $assignmentCount; $i++) {
            $topic->activities()->create([
                'title' => "Tarea {$i} - {$topic->title}",
                'description' => 'Tarea práctica del tema',
                'type' => 'assignment',
                'content' => [
                    'instructions' => 'Instrucciones de la tarea que será editada por el instructor.',
                    'deliverables' => ['Archivo de respuesta', 'Documentación'],
                    'due_date' => null
                ],
                'max_attempts' => 1,
                'max_score' => 100,
            ]);
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