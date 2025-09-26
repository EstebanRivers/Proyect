<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Course;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CourseController extends Controller
{
    use AuthorizesRequests;
    /**
     * Muestrar lista de cursos
     */
    public function index(): View
    {
        $course = Course::all();
        return view('course.index', compact('course'));
    }

    /**
     * Crear un nuevo curso
     */
    public function create(): View
    {
        $course = Course::all();
        return view('course.create', compact('course'));
    }

    /**
     * Guardar un nuevo curso
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credits' => 'required|integer|min:0',
            'hours' => 'required|integer|min:0',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'exists:courses,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $courseData = $validatedData;

        $courseData['instructor_id'] = Auth::id();
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('courses', 'public');
            $courseData['image'] = $path;
        }

         //unset($courseData['image']); 



        if (!empty($validatedData['prerequisites'])) {
            $courseData['prerequisites'] = json_encode($validatedData['prerequisites']);
        }

        $course = Course::create($courseData);

        return redirect()->route('course.topic.create', ['course' => $course->id])->with('success', 'Curso creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $user = Auth::user();
        $course->load('topics.activities');

        // 1. OBTENER LOS NÚMEROS
        // Contamos cuántos temas tiene el curso en total.
        $totalTopics = $course->topics->count();
        // Contamos cuántos ha completado el usuario.
        $completedCount = $user->progress()->whereIn('topic_id', $course->topics->pluck('id'))->count();

        // 2. CALCULAR EL PORCENTAJE (con seguridad para evitar división por cero)
        $progressPercentage = 0; // Valor por defecto
        if ($totalTopics > 0) {
            $progressPercentage = round(($completedCount / $totalTopics) * 100);
        }
        
        // Hacemos lo que ya teníamos: obtener la lista de IDs para marcar los temas
        $completedTopics = $user->progress()->pluck('topic_id');

        // 3. ENVIAR TODO A LA VISTA
        return view('course.show', compact('course', 'completedTopics', 'progressPercentage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        return view('course.edit', ['course' => $course]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        // Autorización: Usamos la Policy para asegurar que el usuario puede editar este curso
        $this->authorize('update', $course);

        // Validación: Las reglas son casi idénticas a las de 'store'
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'credits' => 'required|integer|min:0',
            'hours' => 'required|integer|min:0',
            'prerequisites' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Manejo de la nueva imagen (si se subió una)
        if ($request->hasFile('image')) {
            // Borramos la imagen antigua para no acumular archivos basura
            if ($course->image) {
                Storage::disk('public')->delete($course->image);
            }
            // Guardamos la nueva imagen y actualizamos la ruta
            $validatedData['image'] = $request->file('image')->store('courses', 'public');
        }

        // Actualizamos el curso con los datos validados
        $course->update($validatedData);

         // Si el usuario hizo clic en "Guardar y Editar Temas"
        if ($request->input('action') == 'save_and_continue') {
            return redirect()->route('course.topic.create', ['course' => $course->id])
                    ->with('success', '¡Curso actualizado! Ahora puedes editar sus temas.');
        }


        // Redirigimos al usuario a la lista de cursos con un mensaje de éxito
        return redirect()->route('courses.index')->with('success', '¡Curso actualizado exitosamente!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Curso borrado exitosamente');
    }
}
