<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Curso;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class CursoController extends Controller
{
    /**
     * Muestrar lista de cursos
     */
    public function index(): View
    {
        $cursos = Curso::all();
        return view('cursos.index', compact('cursos'));
    }

    /**
     * Crear un nuevo curso
     */
    public function create(): View
    {
        $cursos = Curso::all();
        return view('cursos.create', compact('cursos'));
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

        $cursoData = $validatedData;

        $cursoData['instructor_id'] = Auth::id();
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/cursos');
            $cursoData['image'] = $path;
        }


        if (!empty($validatedData['prerequisites'])) {
            $cursoData['prerequisites'] = json_encode($validatedData['prerequisites']);
        }

        $cursos = Curso::create($cursoData);

        return redirect()->route('cursos.temas.create', ['curso' => $cursos->id])->with('success', 'Curso creado exitosamente.');
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
