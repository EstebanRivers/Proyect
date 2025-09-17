<?php

namespace App\Http\Controllers;

use App\Models\Activities;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ActivitiesController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'tema_id' => 'required|exists:temas,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:100',
        ]);

        $validatedData['content'] = 'contenido pendiente';
        Activities::create($validatedData);


        return back()->with('success', 'Actividad creada exitosamente.');
    }
}