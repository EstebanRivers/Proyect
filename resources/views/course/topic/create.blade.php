@extends('layouts.app')

@section('title', 'Añadir Temas a ' . $course->title)

@vite(['resources/css/topic.css', 'resources/js/app.js'])

@section('content')
<div class="topics-container">

    {{-- Mensaje de éxito --}}
    @if (session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Encabezado --}}
    <div class="topics-header">
        <div>
            <h1>Añadir Temas y Actividades</h1>
            <h2>Curso: {{ $course->title }}</h2>
        </div>
        <a href="{{ route('courses.index') }}" class="btn-secondary">
            Finalizar
        </a>
    </div>

    <div class="topics-layout">
        {{-- Columna del formulario --}}
        <div class="topics-form">
            @if ($errors->any())
                <div class="alert-danger">
                    <strong>¡Ups! Hubo algunos problemas:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <h3>Añadir Nuevo Tema</h3>
            <form action="{{ route('topics.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="course_id" value="{{ $course->id }}">

                {{-- Título --}}
                <div class="form-group">
                    <label for="title">Título del Tema</label>
                    <input type="text" id="title" name="title" required>
                </div>

                {{-- Descripción --}}
                <div class="form-group">
                    <label for="description">Descripción Detallada del Tema</label>
                    <textarea id="description" name="description" rows="5"></textarea>
                </div>

                {{-- Archivo --}}
                <div class="form-group">
                    <label for="file">Adjuntar Archivo (PDF, Word, PPT)</label>
                    <input type="file" id="file" name="file">
                </div>

                <button type="submit" class="btn-success">+ Añadir Tema</button>
            </form>
        </div>

        {{-- Columna lista de temas --}}
        <div class="topics-list">
            <h3>Temas del Curso ({{ $course->topics->count() }})</h3>
            
            @forelse ($course->topics as $topic)
                <div class="topic-card">
                    <div class="topic-header">
                        <h4>{{ $topic->title }}</h4>

                        {{-- Eliminar tema --}}
                        <form action="{{ route('topics.destroy', $topic) }}" method="POST" onsubmit="return confirm('¿Eliminar este tema y todas sus actividades?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger" title="Eliminar" >
                                <img src="{{asset('icons/trash-solid-full.svg')}}" alt="Eliminar" style="width:24px;height:24px" loading="lazy">
                            </button>
                        </form>
                    </div>

                    <p>{{ $topic->description }}</p>

                    {{-- Archivo adjunto --}}
                    @if ($topic->file_path)
                        <div class="topic-file">
                            <a href="{{ asset('storage/' . $topic->file_path) }}" target="_blank">
                                📎 Ver Archivo Adjunto
                            </a>
                        </div>
                    @endif

                    {{-- Actividades --}}
                    <div class="activities-list">
                        @if($topic->activities->count() > 0)
                            @foreach($topic->activities as $activity)
                                <div class="activity-item">
                                    <strong>{{ $activity->title }}</strong> ({{ $activity->type }})
                                    <form action="{{ route('activities.destroy', $activity) }}" method="POST" onsubmit="return confirm('¿Eliminar esta actividad?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-activity">&times;</button>
                                    </form>
                                </div>
                            @endforeach
                        @else
                            <p class="no-activities">No hay actividades para este tema.</p>
                        @endif
                    </div>

                    {{-- Nueva actividad --}}
                    <div class="activities-layout">
                        <form action="{{ route('activities.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="topic_id" value="{{ $topic->id }}">

                            <h5>Nueva Actividad</h5>
                            
                            <div class="form-group">
                                <input type="text" name="title" placeholder="Título de la actividad" required>
                            </div>

                            <div class="form-group">
                                <select name="type" required class="activity-type-selector">
                                    <option value="" disabled selected>Selecciona el tipo...</option>
                                    <option value="Cuestionario">Cuestionario</option>
                                    <option value="SopaDeLetras">Sopa de Letras</option>
                                </select>
                            </div>

                            <div class="activity-fields-container">
                                <div class="activity-fields" id="fields-Cuestionario">
                                    <div class="form-group">
                                        <label>Pregunta del cuestionario:</label>
                                        <input type="text" name="content[question]" class="form-field-cuestionario" placeholder="Escribe la pregunta aquí">
                                    </div>
                                    <div>
                                        <label>Opciones de respuesta (marca la correcta):</label>
                                        @for ($i = 0; $i < 4; $i++)
                                            <div class="quiz-option">
                                                <input type="radio" name="content[correct_answer]" value="{{ $i }}">
                                                <input type="text" name="content[options][]" class="form-field-cuestionario" placeholder="Opción {{ $i + 1 }}">
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn-primary">+ Añadir Actividad</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="no-topics">
                    <p>Aún no has añadido ningún tema a este curso.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@once
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Seleccionamos todos los selectores de tipo de actividad
        const selectors = document.querySelectorAll('.activity-type-selector');

        selectors.forEach(selector => {
            selector.addEventListener('change', function () {
                const selectedType = this.value;
                const form = this.closest('form');
                
                // Ocultamos todos los campos de actividad dentro de este formulario
                const allFields = form.querySelectorAll('.activity-fields');

                allFields.forEach(field => {
                    field.style.display = 'none';
                    // Deshabilitamos los inputs para que no se envíen si están ocultos
                    field.querySelectorAll('.form-field-cuestionario').forEach(input => input.required = false);
                });

                // Mostramos los campos del tipo seleccionado
                const activeFields = form.querySelector('#fields-' + selectedType);
                if (activeFields) {
                    activeFields.style.display = 'block';
                    // Habilitamos los inputs para que sean requeridos al mostrarse
                    activeFields.querySelectorAll('.form-field-cuestionario').forEach(input => input.required = true);
                }
            });
        });
    });
</script>
@endpush
@endonce
