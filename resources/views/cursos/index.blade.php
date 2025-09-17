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
            <button onclick="window.navigateTo('{{ route('curso.create') }}')" 
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
<div class="courses-container">
    @forelse ($cursos as $curso)
        <div class="course-card">
            <img src="{{ $curso->image_path ?? 'path/to/default/image.jpg' }}" alt="Imagen del curso">
            <div class="course-info">
                <h3 class="course-title">{{ $curso->title }}</h3>
                <p class="course-description">{{ $curso->description }}</p>
                <div class="course-meta">
                    <span>Créditos: {{ $curso->credits }}</span>
                    <span>Horas: {{ $curso->hours }}</span>
                </div>
                <a href="#" class="course-link">Ver Curso</a>
            </div>
        </div>
    @empty
        <div class="no-courses-message">
            <p>Aún no hay cursos disponibles. ¡Vuelve pronto!</p>
        </div>
    @endforelse
</div>
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