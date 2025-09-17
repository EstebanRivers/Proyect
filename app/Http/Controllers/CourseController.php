<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Course;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Muestrar lista de cursos
     */
    public function index(): View
    {
        $courses = Course::all();
        return view('courses.index', compact('courses'));
    }

    /**
     * Crear un nuevo curso
     */
    public function create(): View
    {
        $courses = Course::all();
        return view('courses.create', compact('courses'));
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
            'prerequisites.*' => 'exists:cursos,id',
            'instructor_id' => 'required|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $courseData = $validatedData;

        $courseData['instructor_id'] = Auth::id();
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/cursos');
            $courseData['image'] = $path;
        }


        if (!empty($validatedData['prerequisites'])) {
            $courseData['prerequisites'] = json_encode($validatedData['prerequisites']);
        }

        $courses = Course::create($courseData);

        return redirect()->route('courses.topics.create', ['curso' => $courses->id])->with('success', 'Curso creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
