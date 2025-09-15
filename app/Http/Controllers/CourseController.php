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
        $courses = Course::with(['instructor:id,name', 'prerequisites', 'enrollments'])
            ->where('status', 'activo')
            ->get()
            ->sortBy(function ($curso) {
                return $curso->prerequisites->count();
            });

        // Obtener cursos del usuario si es estudiante o anfitrión
        $userCourses = [];
        if ($user->hasAnyRole(['alumno', 'anfitrion'])) {
            $userCourses = $user->enrollments()
                ->with('curso')
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

            $curso = Course::create($validated);

            // Asignar prerrequisitos
            if ($request->has('prerequisites')) {
                $curso->prerequisites()->attach($request->prerequisites);
            }

            // Crear temas del curso
            if ($request->has('topics')) {
                foreach ($request->topics as $index => $topicData) {
                    $topic = $curso->topics()->create([
                        'title' => $topicData['title'],
                        'description' => $topicData['description'] ?? null,
                        'order' => $index + 1,
                    ]);
                    
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Curso creado exitosamente.',
                    'redirect' => route('courses.builder', $curso)
                ]);
            }

            return redirect()->route('courses.builder', $curso)
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
 * Mostrar formulario para crear/editar temas
 */
public function manageTopics(Course $curso)
{
    $this->authorize('update', $curso);

    $topics = $curso->topics()->with(['contents', 'activities'])->orderBy('order')->get();

    return view('courses.manage-topics', compact('curso', 'topics'));
}

/**
 * Crear nuevo tema
 */
public function storeTopic(Request $request, Course $curso)
{
    $this->authorize('update', $curso);

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'order' => 'required|integer|min:1',
    ]);

    $topic = $curso->topics()->create($validated);

    return response()->json([
        'success' => true,
        'message' => 'Tema creado exitosamente',
        'topic' => $topic
    ]);
}

/**
 * Mostrar formulario para añadir contenido
 */
public function createContent(Course $curso, CourseTopic $topic = null)
{
    $this->authorize('update', $curso);
    
    $topics = $curso->topics()->get();
    
    return view('courses.create-content', compact('curso', 'topics', 'topic'));
}

/**
 * Guardar contenido
 */
public function storeContent(Request $request, Course $curso)
{
    $this->authorize('update', $curso);

    $validated = $request->validate([
        'topic_id' => 'required|exists:course_topics,id,course_id,' . $curso->id,
        'title' => 'required|string|max:255',
        'type' => 'required|in:video,document,presentation,text',
        'description' => 'nullable|string',
        'content' => 'nullable|string',
        'file' => 'nullable|file|mimes:mp4,avi,mov,pdf,doc,docx,txt,jpg,jpeg,png,ppt,pptx|max:10240',
        'duration_minutes' => 'nullable|integer|min:1',
        'order' => 'required|integer|min:0',
    ]);

    try {
        $topic = CourseTopic::findOrFail($validated['topic_id']);

        // Guardar archivo si existe
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('curso-contents', 'public');
        }

        $content = $topic->contents()->create([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'description' => $validated['description'],
            'content' => $validated['content'],
            'file_path' => $filePath,
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'order' => $validated['order'],
        ]);

        return redirect()->route('courses.manage-topics', $curso)
                       ->with('success', 'Contenido agregado exitosamente.');

    } catch (\Exception $e) {
        return back()->withInput()
                    ->withErrors(['error' => 'Error al guardar el contenido: ' . $e->getMessage()]);
    }
}

    public function builder(Course $curso)
    {
        // Retornar la vista del builder (pestañas Temas / Actividades)
        return redirect()->route('courses.manage-topics', $curso);
    }


    /**
     * Mostrar un curso específico
     */
    public function show(Course $curso, Request $request)
    {
        $user = Auth::user();
        
        // Cargar relaciones necesarias
        $curso->load(['instructor', 'prerequisites', 'topics.contents', 'topics.activities']);
        
        // Verificar inscripción del usuario
        $enrollment = null;
        if ($user->hasAnyRole(['alumno', 'anfitrion'])) {
            $enrollment = CourseEnrollment::where('course_id', $curso->id)
                ->where('user_id', $user->id)
                ->first();
        }
        
        // Calcular progreso si está inscrito
        $completedTopics = 0;
        $currentTopic = null;
        
        if ($enrollment && $enrollment->status === 'inscrito') {
            $completedTopics = $curso->topics->filter(function ($topic) use ($user) {
                return $topic->isCompletedByUser($user);
            })->count();
            
            // Obtener tema actual si se especifica
            if ($request->has('topic')) {
                $currentTopic = $curso->topics->find($request->get('topic'));
            }
        }
        
        return view('courses.show', compact('curso', 'enrollment', 'completedTopics', 'currentTopic'));
    }
    
    /**
     * Mostrar contenido de un tema específico (AJAX)
     */
    public function showTopic(Course $curso, CourseTopic $topic)
    {
        $user = Auth::user();
        
        // Verificar que el usuario esté inscrito
        $enrollment = CourseEnrollment::where('course_id', $curso->id)
            ->where('user_id', $user->id)
            ->where('status', 'inscrito')
            ->first();
            
        if (!$enrollment) {
            return response()->json(['error' => 'No estás inscrito en este curso'], 403);
        }
        
        // Verificar que el tema pertenezca al curso
        if ($topic->course_id !== $curso->id) {
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

    /**
     * Inscribirse a un curso
     */
    public function enroll(Course $curso)
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
        if (!$curso->canUserEnroll($user)) {
            $missingPrereqs = $curso->getMissingPrerequisites($user);
            
            if (!empty($missingPrereqs)) {
                $prereqNames = collect($missingPrereqs)->pluck('title')->join(', ');
                return response()->json([
                    'success' => false,
                    'message' => "Debes completar primero: {$prereqNames}"
                ], 400);
            }

            if ($curso->is_full) {
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
                'course_id' => $curso->id,
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
    public function unenroll(Course $curso)
    {
        $user = Auth::user();

        $enrollment = CourseEnrollment::where('course_id', $curso->id)
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