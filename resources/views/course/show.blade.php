@extends('layouts.app')

@section('title', $course->title)

@section('content')
<div class="course-container" style="max-width: 1200px; margin: auto; padding: 20px; font-family: sans-serif;">

    {{-- Encabezado del Curso --}}
    <header style="margin-bottom: 30px;">
        <h1 style="font-size: 2.5em; margin-bottom: 10px;">{{ $course->title }}</h1>
        <p style="font-size: 1.1em; color: #555;">{{ $course->description }}</p>
        {{-- Aquí puedes añadir la barra de progreso más adelante --}}
    </header>

    {{-- Contenido Principal del Curso --}}
    <main>
        @forelse ($course->topics as $topic)
            <div class="topic-card" style="margin-bottom: 25px; background: #f9f9f9; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                
                {{-- Encabezado del Tema --}}
                <h2 style="font-size: 1.8em; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 15px;">{{ $topic->title }}</h2>
                
                {{-- Descripción y Archivos del Tema --}}
                <div class="topic-content" style="margin-bottom: 20px;">
                    <p>{{ $topic->description }}</p>
                    @if ($topic->file_path)
                        <a href="{{ asset('storage/' . $topic->file_path) }}" target="_blank" style="display: inline-block; background: #007bff; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none;">
                            📎 Descargar Material
                        </a>
                    @endif
                </div>

                {{-- Lista de Actividades del Tema --}}
                <div class="activities-list">
                    <h3 style="font-size: 1.4em; margin-bottom: 10px;">Actividades</h3>
                    @forelse ($topic->activities as $activity)
                        <div class="activity-item" style="border-left: 3px solid #e69a37; padding-left: 15px; margin-bottom: 15px;">
                            <h4 style="margin: 0 0 5px 0;">{{ $activity->title }} ({{ $activity->type }})</h4>
                            <p style="margin: 0; color: #666;">{{ $activity->content }}</p>
                            {{-- Aquí irían los botones para que el alumno complete la actividad --}}
                        </div>
                    @empty
                        <p style="color: #888;">Este tema no tiene actividades asignadas.</p>
                    @endforelse
                </div>

            </div>
        @empty
            <div style="text-align: center; padding: 50px; background: #f9f9f9; border-radius: 8px;">
                <p>Este curso aún no tiene temas.</p>
            </div>
        @endforelse
    </main>
</div>
@endsection