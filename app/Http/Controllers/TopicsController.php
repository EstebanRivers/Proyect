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
        return view('course.topic.create', ['course' => $course]);
    }

    /**
     * Guardar un nuevo tema
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'curso_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,pptx,mp4,mov,avi,wmv|max51200',
        ]);

        if ($request->hasFile('file')){
            $path = $request->files('file')->store('topic_files', 'public');

            $validatedData['file_path']=$path;
        }

        Topics::create($validatedData);

        return back()->with('success', 'Tema creado exitosamente.');
    }
}
    
