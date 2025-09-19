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
            $courseData['image_path'] = $path;
        }

         unset($courseData['image']); 



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
        $course->load('topics.activities');

        return view('course.show', ['course'=>$course]);
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
        // 1. Autorización: Usamos la Policy para asegurar que el usuario puede editar este curso
        $this->authorize('update', $course);

        // 2. Validación: Las reglas son casi idénticas a las de 'store'
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'credits' => 'required|integer|min:0',
            'hours' => 'required|integer|min:0',
            'prerequisites' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 3. Manejo de la nueva imagen (si se subió una)
        if ($request->hasFile('image')) {
            // Borramos la imagen antigua para no acumular archivos basura
            if ($course->image_path) {
                Storage::disk('public')->delete($course->image_path);
            }
            // Guardamos la nueva imagen y actualizamos la ruta
            $validatedData['image_path'] = $request->file('image')->store('cursos', 'public');
        }

        // 4. Actualizamos el curso con los datos validados
        $course->update($validatedData);

        // 5. Redirigimos al usuario a la lista de cursos con un mensaje de éxito
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
