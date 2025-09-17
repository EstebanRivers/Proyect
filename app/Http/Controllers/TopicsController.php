<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Topics;
use Illuminate\Http\RedirectResponse;  
use Illuminate\View\View;

class TopicsController extends Controller
{
    /**
     * Mostrar formulario de creación de temas para un curso específico
     */
    public function create(Course $course): View
    {
        $course->load('topics.activities');
        return view('topics.create', ['course' => $course]);
    }

    /**
     * Guardar un nuevo tema
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Topics::create($validatedData);

        return back()->with('success', 'Tema creado exitosamente.');
    }
}
    
