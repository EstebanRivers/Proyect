@extends('layouts.app')

@section('title', 'Crear Curso - Paso 1: Información Básica - UHTA')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <h1 style="color: #333; margin-bottom: 10px; font-size: 28px; font-weight: 600;">Crear Nuevo Curso (Paso 1 de 3)</h1>
        <button onclick="window.navigateTo('{{ route('courses.index') }}')"
                style="background: #6c757d; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
            ← Volver a Cursos
        </button>
    </div>

    <div style="background: white; padding: 40px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
        <form id="createCourseForm" method="POST" action="{{ route('courses.store') }}">
            @csrf
            
            <div style="margin-bottom: 30px;">
                <h3 style="margin: 0 0 20px 0; color: #333; font-size: 18px; font-weight: 600; border-bottom: 2px solid #e69a37; padding-bottom: 8px;">
                    Información Básica
                </h3>
                
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="title" style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                            Título del Curso *
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="ej: Introducción a la Programación" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;" required>
                        @error('title')
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="credits" style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                            Créditos *
                        </label>
                        <input type="number" id="credits" name="credits" value="{{ old('credits', 3) }}" min="1" max="10" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;" required>
                        @error('credits')
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label for="short_description" style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                        Descripción Corta
                    </label>
                    <input type="text" id="short_description" name="short_description" value="{{ old('short_description') }}" placeholder="Breve descripción que aparecerá en la tarjeta del curso" maxlength="500" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
                    @error('short_description')
                        <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div>
                    <label for="description" style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                        Descripción Completa *
                    </label>
                    <textarea id="description" name="description" rows="4" placeholder="Descripción detallada del curso, objetivos, contenido, etc." style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; resize: vertical;" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div style="margin-bottom: 30px;">
                 <h3 style="margin: 0 0 20px 0; color: #333; font-size: 18px; font-weight: 600; border-bottom: 2px solid #e69a37; padding-bottom: 8px;">
                    Configuración del Curso
                </h3>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="difficulty" style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                            Nivel de Dificultad *
                        </label>
                        <select id="difficulty" name="difficulty" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;" required>
                            <option value="basico" {{ old('difficulty') == 'basico' ? 'selected' : '' }}>Básico</option>
                            <option value="intermedio" {{ old('difficulty') == 'intermedio' ? 'selected' : '' }}>Intermedio</option>
                            <option value="avanzado" {{ old('difficulty') == 'avanzado' ? 'selected' : '' }}>Avanzado</option>
                        </select>
                        @error('difficulty')
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="duration_hours" style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                            Duración (horas) *
                        </label>
                        <input type="number" id="duration_hours" name="duration_hours" value="{{ old('duration_hours', 40) }}" min="1" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;" required>
                        @error('duration_hours')
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="instructor_id" style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                            Instructor *
                        </label>
                        <select id="instructor_id" name="instructor_id" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;" required>
                            <option value="">Seleccionar instructor...</option>
                            @foreach($instructors as $instructor)
                                <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                    {{ $instructor->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('instructor_id')
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label for="min_students" style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                            Mín. Estudiantes *
                        </label>
                        <input type="number" id="min_students" name="min_students" value="{{ old('min_students', 5) }}" min="1" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;" required>
                         @error('min_students')
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="max_students" style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                            Máx. Estudiantes *
                        </label>
                        <input type="number" id="max_students" name="max_students" value="{{ old('max_students', 30) }}" min="1" max="100" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;" required>
                         @error('max_students')
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 30px;">
                <h3 style="margin: 0 0 20px 0; color: #333; font-size: 18px; font-weight: 600; border-bottom: 2px solid #e69a37; padding-bottom: 8px;">
                    Prerrequisitos (Opcional)
                </h3>
                
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 12px; color: #555;">
                        Cursos que deben completarse antes de tomar este:
                    </label>
                    
                    @if($availableCourses->count() > 0)
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 12px;">
                            @foreach($availableCourses as $availableCourse)
                                <label style="display: flex; align-items: center; gap: 10px; padding: 12px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; transition: all 0.2s ease;">
                                    <input type="checkbox" name="prerequisites[]" value="{{ $availableCourse->id }}" {{ in_array($availableCourse->id, old('prerequisites', [])) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                                    <div>
                                        <div style="font-weight: 500; color: #333;">{{ $availableCourse->title }}</div>
                                        <div style="font-size: 12px; color: #666;">{{ $availableCourse->difficulty }} • {{ $availableCourse->credits }} créditos</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p style="color: #666; font-style: italic; padding: 20px; background: #f8f9fa; border-radius: 8px; text-align: center;">
                            No hay cursos disponibles para usar como prerrequisitos.
                        </p>
                    @endif
                </div>
            </div>
            
            <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 20px; border-top: 1px solid #eee;">
                <button type="button" onclick="window.navigateTo('{{ route('courses.index') }}')" style="background: #6c757d; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                    Cancelar
                </button>
                <button type="submit" style="background: #e69a37; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;" onclick="disableSPATemporaly()">
                    Guardar y Añadir Temas →
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function disableSPATemporarily() {
    if (window.spaNav) {
        window.spaNav.isLoading = true;
        setTimeout(() => {
            if (window.spaNav) window.spaNav.isLoading = false;
        }, 3000);
    }
}

document.getElementById('createCourseForm').addEventListener('submit', function(e) {
    disableSPATemporarily();
});
</script>
@endpush

{{-- Los bloques de CSS y JS se colocan al final --}}
<style>
input[type="checkbox"]:checked + div {
    color: #e69a37;
}

label:has(input[type="checkbox"]):hover {
    border-color: #e69a37;
    background: #fef9f3;
}

label:has(input[type="checkbox"]:checked) {
    border-color: #e69a37;
    background: #fef9f3;
}
</style>

@push('scripts')
<script>
\document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createCourseForm');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success && data.redirect) {
                // Usar el SPA para navegar
                if (typeof window.navigateTo === 'function') {
                    window.navigateTo(data.redirect);
                } else {
                    // Fallback si no está el SPA
                    window.location.href = data.redirect;
                }
            } else {
                alert(data.message || 'Error al crear el curso');
            }
        } catch (error) {
            console.error(error);
            alert('Error de conexión al crear el curso');
        }
    });
});
</script>
@endpush
<script>
// Validar que max_students sea mayor que min_students al cambiar cualquiera de los dos campos
document.getElementById('min_students').addEventListener('change', validateStudentLimits);
document.getElementById('max_students').addEventListener('change', validateStudentLimits);

function validateStudentLimits() {
    const minStudentsInput = document.getElementById('min_students');
    const maxStudentsInput = document.getElementById('max_students');
    
    // Obtener valores como enteros, tratando los campos vacíos como 0
    const minStudents = parseInt(minStudentsInput.value) || 0;
    const maxStudents = parseInt(maxStudentsInput.value) || 0;
    
    // Solo validar si ambos campos tienen un valor numérico y max es menor que min
    if (minStudents > 0 && maxStudents > 0 && minStudents > maxStudents) {
        alert('El número mínimo de estudiantes no puede ser mayor al máximo. Se ajustará el valor mínimo.');
        // Corregir el valor del campo que se cambió para evitar el conflicto
        minStudentsInput.value = maxStudents;
    }
}
</script>


@endsection