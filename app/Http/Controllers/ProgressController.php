<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Progress;
use App\Models\Topics;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    /**
     * Guarda el progreso de un tema para el usuario autenticado.
     */
    public function store(Request $request)
    {
        // Validamos que el topic_id venga en la petición y que exista en la tabla de temas.
        $request->validate([
            'topic_id' => 'required|exists:topics,id'
        ]);

        // Obtenemos el usuario que está actualmente logueado.
        $user = Auth::user();
        $topicId = $request->topic_id;

        // Usamos firstOrCreate para evitar duplicados.
        // Si ya existe un registro con este user_id y topic_id, no hará nada.
        // Si no existe, lo creará.
        Progress::firstOrCreate([
            'user_id' => $user->id,
            'topic_id' => $topicId,
        ]);

        // Redirigimos al usuario de vuelta a la página del curso.
        return back()->with('status', '¡Progreso guardado!');
    }
}
