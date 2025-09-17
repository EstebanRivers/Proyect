<?php

namespace App\Http\Controllers;

use App\Models\Actividades;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ActividadesController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'tema_id' => 'required|exists:temas,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:100',
        ]);

        $validated['content'] = 'contenido pendiente';
        Actividades::create($validatedData);


        return back()->with('success', 'Actividad creada exitosamente.');
    }
}