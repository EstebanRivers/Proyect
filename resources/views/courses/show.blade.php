@extends('layouts.app')

@section('title', $curso->title . ' - UHTA')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
    <!-- Header del curso -->
    <div style="background: white; border-radius: 16px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
            <div style="flex: 1;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                    <span style="background: #e69a37; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase;">
                        {{ $curso->difficulty }}
                    </span>
                    @if($enrollment)
                        @if($enrollment->status === 'completado')
                            <span style="background: #28a745; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                ✓ Completado
                            </span>
                        @elseif($enrollment->status === 'inscrito')
                            <span style="background: #17a2b8; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                En Progreso
                            </span>
                        @endif
                    @endif
                </div>
                
                <h1 style="margin: 0 0 12px 0; color: #333; font-size: 32px; font-weight: 600; line-height: 1.2;">
                    {{ $curso->title }}
                </h1>
                
                <p style="margin: 0 0 20px 0; color: #666; font-size: 16px; line-height: 1.5;">
                    {{ $curso->description }}
                </p>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; margin-bottom: 20px;">
                    <div>
                        <div style="color: #666; font-size: 14px; margin-bottom: 4px;">Instructor</div>
                        <div style="color: #333; font-weight: 600;">{{ $curso->instructor->name }}</div>
                    </div>
                    <div>
                        <div style="color: #666; font-size: 14px; margin-bottom: 4px;">Duración</div>
                        <div style="color: #333; font-weight: 600;">{{ $curso->duration_hours }} horas</div>
                    </div>
                    <div>
                        <div style="color: #666; font-size: 14px; margin-bottom: 4px;">Créditos</div>
                        <div style="color: #333; font-weight: 600;">{{ $curso->credits }}</div>
                    </div>
                    <div>
                        <div style="color: #666; font-size: 14px; margin-bottom: 4px;">Temas</div>
                        <div style="color: #333; font-weight: 600;">{{ $curso->topics->count() }}</div>
                    </div>
                </div>
                
                @if($enrollment && $enrollment->status === 'inscrito')
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span style="font-weight: 600; color: #333;">Progreso del Curso</span>
                            <span style="color: #666; font-size: 14px;">{{ $completedTopics }}/{{ $curso->topics->count() }} temas</span>
                        </div>
                        <div style="background: #e9ecef; height: 8px; border-radius: 4px; overflow: hidden;">
                            <div style="background: #e69a37; height: 100%; width: {{ $curso->topics->count() > 0 ? ($completedTopics / $curso->topics->count()) * 100 : 0 }}%; transition: width 0.3s ease;"></div>
                        </div>
                    </div>
                @endif
            </div>
            
            <div style="margin-left: 30px;">
                @if(!$enrollment && Auth::user()->hasAnyRole(['alumno', 'anfitrion']))
                    @if($curso->canUserEnroll(Auth::user()))
                        <button onclick="enrollIncurso$curso({{ $curso->id }})"
                                style="background: #28a745; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 16px;">
                            Inscribirse al Curso
                        </button>
                    @else
                        <button disabled
                                style="background: #6c757d; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: not-allowed; font-weight: 600; font-size: 16px;">
                            No Disponible
                        </button>
                    @endif
                @endif
                
                <button onclick="window.navigateTo('{{ route('curso$cursos.index') }}')"
                        style="background: #6c757d; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; margin-top: 10px; display: block; width: 100%;">
                    ← Volver a Cursos
                </button>
            </div>
        </div>
        
        <!-- Prerrequisitos -->
        @if($curso->prerequisites->count() > 0)
            <div style="border-top: 1px solid #eee; padding-top: 20px;">
                <h4 style="margin: 0 0 12px 0; color: #333; font-size: 16px;">Prerrequisitos</h4>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    @foreach($curso->prerequisites as $prereq)
                        @php
                            $hasCompleted = Auth::user()->enrollments()
                                ->where('curso$curso_id', $prereq->id)
                                ->where('status', 'completado')
                                ->exists();
                        @endphp
                        <span style="background: {{ $hasCompleted ? '#d4edda' : '#f8d7da' }}; color: {{ $hasCompleted ? '#155724' : '#721c24' }}; padding: 6px 12px; border-radius: 8px; font-size: 13px; font-weight: 500;">
                            {{ $hasCompleted ? '✓' : '✗' }} {{ $prereq->title }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    
    <!-- Contenido del curso -->
    @if($enrollment && $enrollment->status === 'inscrito')
        <div style="display: grid; grid-template-columns: 300px 1fr; gap: 30px;">
            <!-- Sidebar de navegación -->
            <div style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); height: fit-content; position: sticky; top: 20px;">
                <h3 style="margin: 0 0 20px 0; color: #333; font-size: 18px; font-weight: 600;">Contenido del Curso</h3>
                
                <div style="space-y: 8px;">
                    @foreach($curso->topics as $index => $topic)
                        @php
                            $isCompleted = $topic->isCompletedByUser(Auth::user());
                            $isActive = $currentTopic && $currentTopic->id === $topic->id;
                        @endphp
                        <div style="border-radius: 8px; overflow: hidden; {{ $isActive ? 'background: #fef9f3; border: 2px solid #e69a37;' : 'background: #f8f9fa; border: 2px solid transparent;' }}">
                            <button onclick="loadTopic({{ $topic->id }})"
                                    style="width: 100%; text-align: left; padding: 12px; border: none; background: transparent; cursor: pointer; display: flex; align-items: center; gap: 12px;">
                                <div style="width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; {{ $isCompleted ? 'background: #28a745; color: white;' : ($isActive ? 'background: #e69a37; color: white;' : 'background: #dee2e6; color: #6c757d;') }}">
                                    {{ $isCompleted ? '✓' : $index + 1 }}
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: #333; font-size: 14px; margin-bottom: 2px;">
                                        {{ $topic->title }}
                                    </div>
                                    <div style="color: #666; font-size: 12px;">
                                        {{ $topic->contents->count() }} contenidos • {{ $topic->activities->count() }} actividades
                                    </div>
                                </div>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Contenido principal -->
            <div id="topic-content">
                @if($currentTopic)
                    @include('curso$cursos.partials.topic-content', ['topic' => $currentTopic])
                @else
                    <div style="background: white; border-radius: 16px; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); text-align: center;">
                        <div style="font-size: 48px; margin-bottom: 20px;">📚</div>
                        <h3 style="margin: 0 0 12px 0; color: #333;">¡Bienvenido al curso!</h3>
                        <p style="margin: 0; color: #666;">Selecciona un tema del menú lateral para comenzar a estudiar.</p>
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- Vista para usuarios no inscritos -->
        <div style="background: white; border-radius: 16px; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            <h3 style="margin: 0 0 20px 0; color: #333; font-size: 24px;">Temario del Curso</h3>
            
            <div style="space-y: 16px;">
                @foreach($curso->topics as $index => $topic)
                    <div style="border: 1px solid #eee; border-radius: 12px; padding: 20px;">
                        <div style="display: flex; align-items: flex-start; gap: 16px;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #e69a37; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; flex-shrink: 0;">
                                {{ $index + 1 }}
                            </div>
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 8px 0; color: #333; font-size: 18px; font-weight: 600;">
                                    {{ $topic->title }}
                                </h4>
                                @if($topic->description)
                                    <p style="margin: 0 0 12px 0; color: #666; line-height: 1.5;">
                                        {{ $topic->description }}
                                    </p>
                                @endif
                                <div style="display: flex; gap: 20px; font-size: 14px; color: #666;">
                                    <span>📄 {{ $topic->contents->count() }} contenidos</span>
                                    <span>✏️ {{ $topic->activities->count() }} actividades</span>
                                    @if($topic->total_duration > 0)
                                        <span>⏱️ {{ $topic->total_duration }} min</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if(!Auth::user()->hasAnyRole(['alumno', 'anfitrion']))
                <div style="text-align: center; margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <p style="margin: 0; color: #666;">
                        Solo los estudiantes y anfitriones pueden inscribirse a los cursos.
                    </p>
                </div>
            @endif
        </div>
    @endif
</div>

<script>
function loadTopic(topicId) {
    fetch(`/curso$cursos/{{ $curso->id }}/topics/${topicId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
        }
    })
    .then(response => response.text())
    .then(html => {
        document.getElementById('topic-content').innerHTML = html;
        
        // Actualizar URL sin recargar la página
        const newUrl = `/curso$cursos/{{ $curso->id }}?topic=${topicId}`;
        window.history.pushState({}, '', newUrl);
        
        // Actualizar navegación activa
        updateActiveNavigation(topicId);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar el tema');
    });
}

function updateActiveNavigation(activeTopicId) {
    // Remover clases activas
    document.querySelectorAll('[onclick^="loadTopic"]').forEach(btn => {
        btn.parentElement.style.background = '#f8f9fa';
        btn.parentElement.style.border = '2px solid transparent';
    });
    
    // Agregar clase activa al tema actual
    const activeBtn = document.querySelector(`[onclick="loadTopic(${activeTopicId})"]`);
    if (activeBtn) {
        activeBtn.parentElement.style.background = '#fef9f3';
        activeBtn.parentElement.style.border = '2px solid #e69a37';
    }
}

function enrollIncurso$curso(curso$cursoId) {
    if (!confirm('¿Estás seguro de que quieres inscribirte a este curso?')) {
        return;
    }
    
    fetch(`/curso$cursos/${curso$cursoId}/enroll`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al inscribirse al curso');
    });
}
</script>

<style>
@media (max-width: 768px) {
    .curso$curso-grid {
        grid-template-columns: 1fr !important;
        gap: 20px !important;
    }
    
    .curso$curso-header {
        flex-direction: column !important;
        text-align: center !important;
    }
    
    .curso$curso-header > div:last-child {
        margin-left: 0 !important;
        margin-top: 20px !important;
    }
}
</style>
@endsection