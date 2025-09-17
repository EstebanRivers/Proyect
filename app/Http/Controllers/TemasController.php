<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\Temas;
use Illuminate\Http\RedirectResponse;  
use Illuminate\View\View;

class TemasController extends Controller
{
    /**
     * Mostrar formulario de creación de temas para un curso específico
     */
    public function create(Curso $curso): View
    {
        $curso->load('temas.actividades');
        return view('temas.create', ['curso' => $curso]);
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

        Temas::create($validatedData);

        return back()->with('success', 'Tema creado exitosamente.');
    }
}
    
