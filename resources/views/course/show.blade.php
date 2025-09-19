@extends('layouts.app')

@section('title', $course->title)

@vite(['resources/css/courseShow.css', 'resources/js/app.js'])

@section('content')
<div class="course-viewer-container">

    {{-- ENCABEZADO --}}
    <header class="course-header">
        <h1>{{ $course->title }}</h1>
        <a href="{{ route('courses.index') }}" class="btn-secondary">
            &larr; Volver a Cursos
        </a>
    </header>

    <div class="course-layout">

        {{-- COLUMNA DERECHA (TEMARIO / NAVEGACIÓN) --}}
        <div class="course-syllabus">
            <h3>Contenido del Curso</h3>
            @foreach ($course->topics as $topic)
                <div class="topic-group">
                    <strong class="syllabus-link" data-target="#content-topic-{{ $topic->id }}">
                        {{ $topic->title }}
                    </strong>
                    <ul>
                        @foreach ($topic->activities as $activity)
                            <li class="syllabus-link" data-target="#content-activity-{{ $activity->id }}">
                                - {{ $activity->title }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        {{-- COLUMNA IZQUIERDA (VISOR DE CONTENIDO) --}}
        <div class="content-viewer">

            {{-- Contenido por defecto --}}
            <div class="content-panel" id="content-default" style="display: block;">
                <h2>Bienvenido al curso</h2>
                <p>Selecciona un tema o actividad de la lista de la derecha para comenzar.</p>
                @if($course->image_path)
                    <img src="{{ asset('storage/' . $course->image_path) }}" alt="Portada del curso" class="course-cover">
                @endif
            </div>

            {{-- Paneles dinámicos --}}
            @foreach ($course->topics as $topic)
                {{-- Panel Tema --}}
                <div class="content-panel" id="content-topic-{{ $topic->id }}">
                    <h2>{{ $topic->title }}</h2>
                    <p>{{ $topic->description }}</p>
                    @if ($topic->file_path)
                        <a href="{{ asset('storage/' . $topic->file_path) }}" target="_blank" class="btn-primary">
                            📎 Ver/Descargar Material del Tema
                        </a>
                    @endif
                </div>

                {{-- Panel Actividades --}}
                @foreach ($topic->activities as $activity)
                    <div class="content-panel" id="content-activity-{{ $activity->id }}">
                        <h3>{{ $activity->title }} ({{ $activity->type }})</h3>
                        
                        @if ($activity->type == 'Cuestionario' && is_array($activity->content))
                            <form action="#" method="POST">
                                @csrf
                                <p class="question-text">
                                    {{ $activity->content['question'] ?? '' }}
                                </p>
                                
                                @foreach ($activity->content['options'] as $index => $option)
                                    <div class="option-box">
                                        <label>
                                            <input type="radio" name="answer" value="{{ $index }}" {{ Auth::id() == $course->instructor_id ? 'disabled' : '' }}>
                                            {{ $option }}
                                        </label>
                                    </div>
                                @endforeach

                                @if (Auth::id() != $course->instructor_id)
                                    <button type="submit" class="btn-success">Enviar Respuesta</button>
                                @else
                                    <p class="instructor-note">(Vista de previsualización para el instructor)</p>
                                @endif
                            </form>
                        @else
                            <p>{{ is_array($activity->content) ? json_encode($activity->content) : $activity->content }}</p>
                        @endif
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const links = document.querySelectorAll('.syllabus-link');
        const contentPanels = document.querySelectorAll('.content-panel');
        const syllabusListItems = document.querySelectorAll('.course-syllabus .syllabus-link');

        links.forEach(link => {
            link.addEventListener('click', function () {
                const targetId = this.dataset.target;

                // Ocultar todos los paneles
                contentPanels.forEach(panel => panel.style.display = 'none');

                // Mostrar panel elegido
                const targetPanel = document.querySelector(targetId);
                if (targetPanel) targetPanel.style.display = 'block';
                
                // Resaltar link activo
                syllabusListItems.forEach(item => item.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });
</script>
@endpush
@endsection
