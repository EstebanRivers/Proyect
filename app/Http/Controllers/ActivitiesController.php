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
            'topic_id' => 'required|exists:topics,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'content' => 'required|array',
        ]);

        if ($validatedData['type']==='Cuestionario'){
            $request->validate([
                'content.question' => 'required|string',
                'content.options' => 'required|array|min:4',
                'content.options.*' => 'required|string',
                'content.correct.answer' => 'required',

            ]);
        }

        Activities::create($validatedData);


        return back()->with('success', 'Actividad creada exitosamente.');
    }
}