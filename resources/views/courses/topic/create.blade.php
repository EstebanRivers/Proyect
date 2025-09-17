@extends('layouts.app')

@section('title', 'Añadir Temas a ' . $course->title)

@section('content')
<div style="max-width: 900px; margin: 0 auto; padding: 20px;">

    {{-- Mensaje de éxito --}}
    @if (session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="color: #333; margin-bottom: 0;">Añadir Temas y Actividades</h1>
            <h2 style="color: #e69a37; margin-top: 5px; font-weight: 500;">Curso: {{ $course->title }}</h2>
        </div>
        <a href="{{ route('courses.index') }}" 
           style="background: #6c757d; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            Finalizar
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
        {{-- Columna del formulario para añadir un nuevo tema --}}
        <div style="background: #f8f9fa; padding: 20px; border-radius: 12px;">
            <h3 style="margin-top: 0; color: #333;">Añadir Nuevo Tema</h3>
            <form action="{{ route('temas.store') }}" method="POST">
                @csrf
                <input type="hidden" name="curso_id" value="{{ $course->id }}">

                <div style="margin-bottom: 15px;">
                    <label for="title" style="display: block; margin-bottom: 5px; font-weight: 600;">Título del Tema</label>
                    <input type="text" id="title" name="title" required
                           style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="description" style="display: block; margin-bottom: 5px; font-weight: 600;">Descripción (opcional)</label>
                    <textarea id="description" name="description" rows="3"
                              style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;"></textarea>
                </div>

                <button type="submit"
                        style="background: #28a745; color: white; width: 100%; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                    + Añadir Tema
                </button>
            </form>
        </div>

        {{-- Columna para listar los temas existentes --}}
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <h3 style="margin-top: 0; color: #333;">Temas del Curso ({{ $course->topics->count() }})</h3>
            
            @forelse ($course->topics as $topic)
                <div style="background: #f8f9fa; border-radius: 8px; padding: 15px; margin-bottom: 15px;">
                    {{-- Título del Tema --}}
                    <h4 style="margin: 0 0 10px 0;">{{ $topic->title }}</h4>
                    <p style="margin: 0 0 15px 0; color: #666; font-size: 14px;">{{ $topic->description }}</p>

                    {{-- Lista de Actividades Existentes --}}
                    <div style="margin-bottom: 15px;">
                        @if($topic->activities->count() > 0)
                            @foreach($topic->activities as $activity)
                                <div style="font-size: 14px; padding: 5px 0; border-bottom: 1px solid #ddd;">
                                    <strong>{{ $activity->title }}</strong> ({{ $activity->type }})
                                </div>
                            @endforeach
                        @else
                            <p style="font-size: 13px; color: #888;">No hay actividades para este tema.</p>
                        @endif
                    </div>

                    {{-- Formulario para Añadir Nueva Actividad --}}
                    <form action="{{ route('activities.store') }}" method="POST"> {{-- La acción la definiremos en el siguiente paso --}}
                        @csrf
                        <input type="hidden" name="topic_id" value="{{ $topic->id }}">
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <input type="text" name="title" placeholder="Título de la nueva actividad" required style="flex: 1; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
                            <select name="type" style="padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
                                <option value="lectura">Lectura</option>
                                <option value="video">Video</option>
                                <option value="cuestionario">Cuestionario</option>
                            </select>
                            <button type="submit" style="background: #007bff; color: white; padding: 8px 12px; border: none; border-radius: 6px; cursor: pointer;">+</button>
                        </div>
                        {{-- Campo de contenido (lo añadiremos después para que no sea muy complejo) --}}
                    </form>
                </div>
            @empty
                <div style="color: #666; text-align: center; padding: 40px 0;">
                    <p>Aún no has añadido ningún tema a este curso.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection