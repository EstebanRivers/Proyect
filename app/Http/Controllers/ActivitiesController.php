<?php

namespace App\Http\Controllers;

use App\Models\Activities;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Progress;
use Illuminate\Support\Facades\Auth; 

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
                'content.correct_answer' => 'required',

            ]);
        }

        Activities::create($validatedData);

        return back()->with('success', 'Actividad creada exitosamente.');
    }

    public function destroy(Activities $activity)
    {
        // 1. Elimina la actividad específica
        $activity->delete();

        // 2. Redirige al usuario a la página anterior con un mensaje de éxito
        return back()->with('success', '¡Actividad eliminada exitosamente!');
    }

    public function checkAnswer(Request $request, Activities $activity)
    {
        $request->validate(['answer' => 'required']);

        // El ÍNDICE que envió el usuario (ej: 2)
        $userAnswerIndex = $request->input('answer');

        // El ÍNDICE correcto que viene de tu base de datos (ej: 2)
        $correctAnswerIndex = $activity->content['correct_answer'];

        // ¡LA COMPARACIÓN CORRECTA! Comparamos el índice del usuario (2) con el índice de la BD (2).
        if ($userAnswerIndex == $correctAnswerIndex) {
            $user = Auth::user();
            Progress::firstOrCreate([
                'user_id' => $user->id,
                'topic_id' => $activity->topic_id,
            ]);
            $result = '¡Correcto! Tema marcado como completado.';
        } else {
            $options = $activity->content['options'] ?? [];
            $correctAnswerText = $options[$correctAnswerIndex] ?? 'Respuesta no encontrada';
            $result = 'Inténtalo de nuevo. La respuesta correcta era: ' . $correctAnswerText;
        }

        // Solución al problema #1: Regresar a la actividad correcta.
        // Añadimos un "ancla" a la URL para que la página salte a la actividad.
        $course = $activity->topic->course; 

        return redirect()->route('course.show', $course->id)->with('quiz_result', $result)->withFragment('content-activity-' . $activity->id);
    }

}