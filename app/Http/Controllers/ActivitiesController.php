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
            'tema_id' => 'required|exists:topics,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'content' => 'required|string',
        ]);

        Activities::create($validatedData);


        return back()->with('success', 'Actividad creada exitosamente.');
    }
}