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
            @if ($errors->any())
                <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <strong>¡Ups! Hubo algunos problemas:</strong>
                    <ul style="margin-top: 10px; padding-left: 20px; margin-bottom: 0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <h3 style="margin-top: 0; color: #333;">Añadir Nuevo Tema</h3>
            <form action="{{ route('topics.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="course_id" value="{{ $course->id }}">

                {{-- Título del Tema --}}
                <div style="margin-bottom: 15px;">
                    <label for="title" style="display: block; margin-bottom: 5px; font-weight: 600;">Título del Tema</label>
                    <input type="text" id="title" name="title" required
                        style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
                </div>

                {{-- Descripción (más grande) --}}
                <div style="margin-bottom: 15px;">
                    <label for="description" style="display: block; margin-bottom: 5px; font-weight: 600;">Descripción Detallada del Tema</label>
                    <textarea id="description" name="description" rows="5"
                            style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;"></textarea>
                </div>

                {{-- NUEVO CAMPO PARA SUBIR ARCHIVO --}}
                <div style="margin-bottom: 20px;">
                    <label for="file" style="display: block; margin-bottom: 5px; font-weight: 600;">Adjuntar Archivo (PDF, Word, PPT)</label>
                    <input type="file" id="file" name="file"
                        style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc; background: white;">
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

                    @if ($topic->file_path)
                        <div style="margin-bottom: 15px;">
                            <a href="{{ asset('storage/' . $topic->file_path) }}" target="_blank" 
                            style="display: inline-flex; align-items: center; text-decoration: none; font-size: 14px; color: #007bff; font-weight: 500;">
                                📎 Ver Archivo Adjunto
                            </a>
                        </div>
                    @endif

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
                         <h5 style="margin-top: 20px; margin-bottom: 10px; color: #333; border-top: 1px solid #ddd; padding-top: 15px;">Nueva Actividad</h5>
                        <div style="margin-bottom: 10px;">
                            <input type="text" name="title" placeholder="Título de la actividad (ej. Resumen del Tema 1)" required 
                                style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
                        </div>

                        <div style="margin-bottom: 10px;">
                            <select name="type" required style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc; background: white;">
                                <option value="" disabled selected>Selecciona el tipo de actividad...</option>
                                <option value="Resumen">Resumen</option>
                                <option value="Preguntas">Preguntas</option>
                            </select>
                        </div>

                        <div style="margin-bottom: 10px;">
                            <textarea name="content" rows="4" placeholder="Escribe aquí las instrucciones, el resumen a realizar o las preguntas para el estudiante..." required 
                                    style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #ccc;"></textarea>
                        </div>

                        <button type="submit" style="background: #007bff; color: white; width: 100%; padding: 10px; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                            + Añadir Actividad
                        </button>
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