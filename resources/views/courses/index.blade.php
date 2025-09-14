@extends('layouts.app')

@section('title', 'Cursos - UHTA')

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 20px;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
        <div>
            <h1 style="color: #333; margin: 0 0 8px 0; font-size: 32px; font-weight: 600;">Cursos Disponibles</h1>
            <p style="color: #666; margin: 0; font-size: 16px;">
                @if(Auth::user()->hasAnyRole(['admin', 'docente']))
                    Gestiona y crea cursos para los estudiantes
                @else
                    Explora y inscríbete a los cursos disponibles
                @endif
            </p>
        </div>
        
        @if(Auth::user()->hasAnyRole(['admin', 'docente']))
            <button onclick="window.navigateTo('{{ route('courses.create') }}')" 
                    style="background: #e69a37; color: white; padding: 14px 28px; border: none; border-radius: 12px; cursor: pointer; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(230, 154, 55, 0.3); transition: all 0.2s ease;">
                + Crear Curso
            </button>
        @endif
    </div>

    <!-- Filtros rápidos -->
    <div style="display: flex; gap: 12px; margin-bottom: 30px; flex-wrap: wrap;">
        <button class="filter-btn active" data-filter="all" 
                style="padding: 8px 16px; border: 2px solid #e69a37; background: #e69a37; color: white; border-radius: 20px; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.2s ease;">
            Todos
        </button>
        <button class="filter-btn" data-filter="basico"
                style="padding: 8px 16px; border: 2px solid #e69a37; background: transparent; color: #e69a37; border-radius: 20px; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.2s ease;">
            Básico
        </button>
        <button class="filter-btn" data-filter="intermedio"
                style="padding: 8px 16px; border: 2px solid #e69a37; background: transparent; color: #e69a37; border-radius: 20px; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.2s ease;">
            Intermedio
        </button>
        <button class="filter-btn" data-filter="avanzado"
                style="padding: 8px 16px; border: 2px solid #e69a37; background: transparent; color: #e69a37; border-radius: 20px; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.2s ease;">
            Avanzado
        </button>
        @if(Auth::user()->hasAnyRole(['alumno', 'anfitrion']))
        <button class="filter-btn" data-filter="available"
                style="padding: 8px 16px; border: 2px solid #28a745; background: transparent; color: #28a745; border-radius: 20px; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.2s ease;">
            Disponibles para mí
        </button>
        <button class="filter-btn" data-filter="enrolled"
                style="padding: 8px 16px; border: 2px solid #17a2b8; background: transparent; color: #17a2b8; border-radius: 20px; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.2s ease;">
            Mis Cursos
        </button>
        @endif
    </div>

    <!-- Grid de cursos -->
    <div id="courses-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 24px; margin-bottom: 40px;">
        @forelse($courses as $course)
            @php
                $userEnrollment = $userCourses[$course->id] ?? null;
                $canEnroll = Auth::user()->hasAnyRole(['alumno', 'anfitrion']) && $course->canUserEnroll(Auth::user());
                $missingPrereqs = $course->getMissingPrerequisites(Auth::user());
                $isEnrolled = $userEnrollment && $userEnrollment->status === 'inscrito';
                $isCompleted = $userEnrollment && $userEnrollment->status === 'completado';
            @endphp
            
            <div class="course-card" 
                 data-difficulty="{{ $course->difficulty }}"
                 data-available="{{ $canEnroll ? 'true' : 'false' }}"
                 data-enrolled="{{ $isEnrolled ? 'true' : 'false' }}"
                 style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); transition: all 0.3s ease; position: relative;">
                
                <!-- Imagen del curso -->
                <div style="height: 180px; background: linear-gradient(135deg, #e69a37, #f4a261); position: relative; overflow: hidden;">
                    @if($course->image)
                        <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" 
                             style="width: 100%; height: 100%; object-fit: cover;">
                    @endif
                    
                    <!-- Badge de dificultad -->
                    <div style="position: absolute; top: 12px; left: 12px;">
                        <span style="background: rgba(0,0,0,0.7); color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase;">
                            {{ $course->difficulty }}
                        </span>
                    </div>
                    
                    <!-- Badge de estado -->
                    @if($isCompleted)
                        <div style="position: absolute; top: 12px; right: 12px;">
                            <span style="background: #28a745; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                ✓ Completado
                            </span>
                        </div>
                    @elseif($isEnrolled)
                        <div style="position: absolute; top: 12px; right: 12px;">
                            <span style="background: #17a2b8; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                Inscrito
                            </span>
                        </div>
                    @endif
                    
                    <!-- Indicador de progreso de cupos -->
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 4px; background: rgba(0,0,0,0.2);">
                        <div style="height: 100%; background: #fff; width: {{ $course->progress }}%; transition: width 0.3s ease;"></div>
                    </div>
                </div>
                
                <!-- Contenido -->
                <div style="padding: 20px;">
                    <!-- Título y código -->
                    <div style="margin-bottom: 12px;">
                        <h3 style="margin: 0 0 4px 0; font-size: 18px; font-weight: 600; color: #333; line-height: 1.3;">
                            {{ $course->title }}
                        </h3>
                        <p style="margin: 0; font-size: 12px; color: #666; font-family: monospace;">
                            {{ $course->code }}
                        </p>
                    </div>
                    
                    <!-- Descripción corta -->
                    <p style="margin: 0 0 16px 0; color: #666; font-size: 14px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $course->short_description ?: Str::limit($course->description, 100) }}
                    </p>
                    
                    <!-- Información del curso -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; font-size: 13px;">
                        <div style="display: flex; align-items: center; gap: 6px; color: #666;">
                            <span style="font-weight: 600;">👨‍🏫</span>
                            {{ $course->instructor->name }}
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px; color: #666;">
                            <span style="font-weight: 600;">⏱️</span>
                            {{ $course->duration_hours }}h
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px; color: #666;">
                            <span style="font-weight: 600;">🎓</span>
                            {{ $course->credits }} créditos
                        </div>
                        <div style="display: flex; align-items: center; gap: 6px; color: #666;">
                            <span style="font-weight: 600;">👥</span>
                            {{ $course->enrolled_count }}/{{ $course->max_students }}
                        </div>
                    </div>
                    
                    <!-- Prerrequisitos -->
                    @if($course->prerequisites->count() > 0)
                        <div style="margin-bottom: 16px;">
                            <p style="margin: 0 0 6px 0; font-size: 12px; font-weight: 600; color: #666; text-transform: uppercase;">
                                Prerrequisitos:
                            </p>
                            <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                @foreach($course->prerequisites as $prereq)
                                    @php
                                        $hasCompleted = Auth::user()->enrollments()
                                            ->where('course_id', $prereq->id)
                                            ->where('status', 'completado')
                                            ->exists();
                                    @endphp
                                    <span style="background: {{ $hasCompleted ? '#d4edda' : '#f8d7da' }}; color: {{ $hasCompleted ? '#155724' : '#721c24' }}; padding: 2px 8px; border-radius: 8px; font-size: 11px; font-weight: 500;">
                                        {{ $hasCompleted ? '✓' : '✗' }} {{ $prereq->code }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- Precio -->
                    @if($course->price > 0)
                        <div style="margin-bottom: 16px;">
                            <span style="font-size: 20px; font-weight: 700; color: #e69a37;">
                                ${{ number_format($course->price, 2) }}
                            </span>
                        </div>
                    @else
                        <div style="margin-bottom: 16px;">
                            <span style="font-size: 16px; font-weight: 600; color: #28a745;">
                                Gratuito
                            </span>
                        </div>
                    @endif
                    
                    <!-- Botones de acción -->
                    <div style="display: flex; gap: 8px;">
                        @if(Auth::user()->hasAnyRole(['alumno', 'anfitrion']))
                            @if($isEnrolled)
                                <button onclick="unenrollFromCourse({{ $course->id }})"
                                        style="flex: 1; background: #dc3545; color: white; padding: 10px; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; font-size: 14px;">
                                    Desinscribirse
                                </button>
                            @elseif($canEnroll)
                                <button onclick="enrollInCourse({{ $course->id }})"
                                        style="flex: 1; background: #28a745; color: white; padding: 10px; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; font-size: 14px;">
                                    Inscribirse
                                </button>
                            @else
                                <button disabled
                                        style="flex: 1; background: #6c757d; color: white; padding: 10px; border: none; border-radius: 8px; cursor: not-allowed; font-weight: 500; font-size: 14px;"
                                        title="{{ !empty($missingPrereqs) ? 'Faltan prerrequisitos' : ($course->is_full ? 'Curso lleno' : 'No disponible') }}">
                                    {{ !empty($missingPrereqs) ? 'Prerrequisitos' : ($course->is_full ? 'Lleno' : 'No disponible') }}
                                </button>
                            @endif
                        @endif
                        
                        @if(Auth::user()->hasAnyRole(['admin', 'docente']))
                            <button onclick="window.navigateTo('/courses/{{ $course->id }}/edit')"
                                    style="background: #17a2b8; color: white; padding: 10px 16px; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; font-size: 14px;">
                                Editar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: #666;">
                <div style="font-size: 48px; margin-bottom: 16px;">📚</div>
                <h3 style="margin: 0 0 8px 0; color: #333;">No hay cursos disponibles</h3>
                <p style="margin: 0;">
                    @if(Auth::user()->hasAnyRole(['admin', 'docente']))
                        Comienza creando el primer curso para los estudiantes.
                    @else
                        Los cursos estarán disponibles próximamente.
                    @endif
                </p>
            </div>
        @endforelse
    </div>
</div>

<style>
.course-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12) !important;
}

.filter-btn:hover {
    background: #e69a37 !important;
    color: white !important;
}

.filter-btn.active {
    background: #e69a37 !important;
    color: white !important;
}

@media (max-width: 768px) {
    #courses-grid {
        grid-template-columns: 1fr !important;
        gap: 16px !important;
    }
}
</style>

<script>
// Filtros
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const courseCards = document.querySelectorAll('.course-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Actualizar botones activos
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            
            courseCards.forEach(card => {
                let show = false;
                
                switch(filter) {
                    case 'all':
                        show = true;
                        break;
                    case 'basico':
                    case 'intermedio':
                    case 'avanzado':
                        show = card.dataset.difficulty === filter;
                        break;
                    case 'available':
                        show = card.dataset.available === 'true';
                        break;
                    case 'enrolled':
                        show = card.dataset.enrolled === 'true';
                        break;
                }
                
                card.style.display = show ? 'block' : 'none';
            });
        });
    });
});

// Inscripción a cursos
function enrollInCourse(courseId) {
    if (!confirm('¿Estás seguro de que quieres inscribirte a este curso?')) {
        return;
    }
    
    fetch(`/courses/${courseId}/enroll`, {
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

// Desinscripción de cursos
function unenrollFromCourse(courseId) {
    if (!confirm('¿Estás seguro de que quieres desinscribirte de este curso?')) {
        return;
    }
    
    fetch(`/courses/${courseId}/unenroll`, {
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
        alert('Error al desinscribirse del curso');
    });
}
</script>
@endsection