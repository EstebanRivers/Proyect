@extends('layouts.app')

@section('title', 'Crear Curso - UHTA')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <h1 style="color: #333; margin-bottom: 10px; font-size: 28px; font-weight: 600;">Crear Nuevo Curso</h1>
        <button onclick="window.navigateTo('{{ route('courses.index') }}')"
                style="background: #6c757d; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
            ← Volver a Cursos
        </button>
    </div>

    <div style="background: white; padding: 40px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
        <form id="createCourseForm" method="POST" action="{{ route('courses.store') }}">
            @csrf
            
            <!-- Información básica -->
            <div style="margin-bottom: 30px;">
                <h3 style="margin: 0 0 20px 0; color: #333; font-size: 18px; font-weight: 600; border-bottom: 2px solid #e69a37; padding-bottom: 8px;">
                    Información Básica
                </h3>
                
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="title" style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                            Título del Curso *
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}"
                               placeholder="ej: Introducción a la Programación"
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;"
                               required>
                        @error('title')
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="credits" style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                            Créditos *
                        </label>
                        <input type="number" 
                               id="credits" 
                               name="credits" 
                               value="{{ old('credits', 3) }}"
                               min="1" max="10"
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;"
                               required>
                        @error('credits')
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label for="short_description" style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                        Descripción Corta
                    </label>
                    <input type="text" 
                           id="short_description" 
                           name="short_description" 
                           value="{{ old('short_description') }}"
                           placeholder="Breve descripción que aparecerá en la tarjeta del curso"
                           maxlength="500"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
                    @error('short_description')
                        <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div>
                    <label for="description" style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                        Descripción Completa *
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              placeholder="Descripción detallada del curso, objetivos, contenido, etc."
                              style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; resize: vertical;"
                              required>{{ old('description') }}</textarea>
                    @error('description')
                        <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <!-- Configuración del curso -->
            <div style="margin-bottom: 30px;">
                <h3 style="margin: 0 0 20px 0; color: #333; font-size: 18px; font-weight: 600; border-bottom: 2px solid #e69a37; padding-bottom: 8px;">
                    Configuración del Curso
                </h3>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="difficulty" style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                            Nivel de Dificultad *
                        </label>
                        <select id="difficulty" 
                                name="difficulty" 
                                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;"
                                required>
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
                        <input type="number" 
                               id="duration_hours" 
                               name="duration_hours" 
                               value="{{ old('duration_hours', 40) }}"
                               min="1"
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;"
                               required>
                        @error('duration_hours')
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="instructor_id" style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                            Instructor *
                        </label>
                        <select id="instructor_id" 
                                name="instructor_id" 
                                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;"
                                required>
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
                        <input type="number" 
                               id="min_students" 
                               name="min_students" 
                               value="{{ old('min_students', 5) }}"
                               min="1"
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;"
                               required>
                        @error('min_students')
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="max_students" style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                            Máx. Estudiantes *
                        </label>
                        <input type="number" 
                               id="max_students" 
                               name="max_students" 
                               value="{{ old('max_students', 30) }}"
                               min="1" max="100"
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;"
                               required>
                        @error('max_students')
                            <div style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Contenido del Curso -->
            <div style="margin-bottom: 30px;">
                <h3 style="margin: 0 0 20px 0; color: #333; font-size: 18px; font-weight: 600; border-bottom: 2px solid #e69a37; padding-bottom: 8px;">
                    Contenido del Curso
                </h3>
                
                <div style="margin-bottom: 20px;">
                    <label for="topics_count" style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                        ¿Cuántos temas tendrá el curso? *
                    </label>
                    <input type="number" 
                           id="topics_count" 
                           name="topics_count" 
                           value="{{ old('topics_count', 1) }}"
                           min="1" max="20"
                           style="width: 200px; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;"
                           required>
                    <button type="button" 
                            onclick="generateTopicsForm()" 
                            style="margin-left: 10px; background: #17a2b8; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                        Generar Temas
                    </button>
                </div>
                
                <div id="topics-container" style="display: none;">
                    <!-- Los temas se generarán dinámicamente aquí -->
                </div>
            </div>
            
            <!-- Prerrequisitos -->
            <div style="margin-bottom: 30px;">
                <h3 style="margin: 0 0 20px 0; color: #333; font-size: 18px; font-weight: 600; border-bottom: 2px solid #e69a37; padding-bottom: 8px;">
                    Prerrequisitos
                </h3>
                
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 12px; color: #555;">
                        Cursos que deben completarse antes de tomar este curso:
                    </label>
                    
                    @if($availableCourses->count() > 0)
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 12px;">
                            @foreach($availableCourses as $availableCourse)
                                <label style="display: flex; align-items: center; gap: 10px; padding: 12px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; transition: all 0.2s ease;">
                                    <input type="checkbox" 
                                           name="prerequisites[]" 
                                           value="{{ $availableCourse->id }}"
                                           {{ in_array($availableCourse->id, old('prerequisites', [])) ? 'checked' : '' }}
                                           style="width: 18px; height: 18px;">
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
            
            <!-- Botones -->
            <div style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 20px; border-top: 1px solid #eee;">
                <button type="button" 
                        onclick="window.navigateTo('{{ route('courses.index') }}')"
                        style="background: #6c757d; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                    Cancelar
                </button>
                <button type="submit"
                        style="background: #e69a37; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                    Crear Curso
                </button>
            </div>
        </form>
    </div>
</div>

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

<script>
let topicsGenerated = false;

function generateTopicsForm() {
    const topicsCount = parseInt(document.getElementById('topics_count').value);
    const container = document.getElementById('topics-container');
    
    if (topicsCount < 1 || topicsCount > 20) {
        alert('El número de temas debe estar entre 1 y 20');
        return;
    }
    
    let html = '<h4 style="margin-bottom: 20px; color: #333;">Configuración de Temas</h4>';
    
    for (let i = 1; i <= topicsCount; i++) {
        html += `
            <div style="background: #f8f9fa; padding: 20px; border-radius: 12px; margin-bottom: 20px; border-left: 4px solid #e69a37;">
                <h5 style="margin: 0 0 15px 0; color: #e69a37; font-size: 16px;">Tema ${i}</h5>
                
                <div style="display: grid; gap: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                            Título del Tema *
                        </label>
                        <input type="text" 
                               name="topics[${i-1}][title]" 
                               placeholder="ej: Introducción a Variables"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;"
                               required>
                    </div>
                    
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">
                            Descripción del Tema
                        </label>
                        <textarea name="topics[${i-1}][description]" 
                                  rows="2"
                                  placeholder="Breve descripción de lo que se cubrirá en este tema"
                                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; resize: vertical;"></textarea>
                    </div>
                    
                    <!-- Contenidos del tema -->
                    <div>
                        <h6 style="margin: 15px 0 10px 0; color: #333; font-size: 14px;">Contenidos del Tema</h6>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div>
                                <label style="display: block; font-weight: 500; margin-bottom: 5px; color: #555; font-size: 13px;">
                                    Número de Videos
                                </label>
                                <input type="number" 
                                       name="topics[${i-1}][videos_count]" 
                                       min="0" max="10" value="0"
                                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                            </div>
                            <div>
                                <label style="display: block; font-weight: 500; margin-bottom: 5px; color: #555; font-size: 13px;">
                                    Número de Documentos
                                </label>
                                <input type="number" 
                                       name="topics[${i-1}][documents_count]" 
                                       min="0" max="10" value="0"
                                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                            </div>
                            <div>
                                <label style="display: block; font-weight: 500; margin-bottom: 5px; color: #555; font-size: 13px;">
                                    Número de Presentaciones
                                </label>
                                <input type="number" 
                                       name="topics[${i-1}][presentations_count]" 
                                       min="0" max="10" value="0"
                                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                            </div>
                            <div>
                                <label style="display: block; font-weight: 500; margin-bottom: 5px; color: #555; font-size: 13px;">
                                    Contenido de Texto
                                </label>
                                <select name="topics[${i-1}][has_text_content]" 
                                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                                    <option value="0">No</option>
                                    <option value="1">Sí</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actividades del tema -->
                    <div>
                        <h6 style="margin: 15px 0 10px 0; color: #333; font-size: 14px;">Actividades del Tema</h6>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                            <div>
                                <label style="display: block; font-weight: 500; margin-bottom: 5px; color: #555; font-size: 13px;">
                                    Cuestionarios de Opción Múltiple
                                </label>
                                <input type="number" 
                                       name="topics[${i-1}][quiz_multiple_count]" 
                                       min="0" max="5" value="0"
                                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                            </div>
                            <div>
                                <label style="display: block; font-weight: 500; margin-bottom: 5px; color: #555; font-size: 13px;">
                                    Cuestionarios Abiertos
                                </label>
                                <input type="number" 
                                       name="topics[${i-1}][quiz_open_count]" 
                                       min="0" max="5" value="0"
                                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                            </div>
                            <div>
                                <label style="display: block; font-weight: 500; margin-bottom: 5px; color: #555; font-size: 13px;">
                                    Ensayos
                                </label>
                                <input type="number" 
                                       name="topics[${i-1}][essay_count]" 
                                       min="0" max="3" value="0"
                                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                            </div>
                            <div>
                                <label style="display: block; font-weight: 500; margin-bottom: 5px; color: #555; font-size: 13px;">
                                    Tareas/Asignaciones
                                </label>
                                <input type="number" 
                                       name="topics[${i-1}][assignment_count]" 
                                       min="0" max="3" value="0"
                                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    container.innerHTML = html;
    container.style.display = 'block';
    topicsGenerated = true;
}

document.getElementById('createCourseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!topicsGenerated) {
        alert('Por favor genera los temas del curso antes de continuar');
        return;
    }
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.navigateTo(data.redirect);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al crear el curso');
    });
});

// Validar que max_students sea mayor que min_students
document.getElementById('min_students').addEventListener('change', validateStudentLimits);
document.getElementById('max_students').addEventListener('change', validateStudentLimits);

function validateStudentLimits() {
    const minStudents = parseInt(document.getElementById('min_students').value);
    const maxStudents = parseInt(document.getElementById('max_students').value);
    
    if (minStudents && maxStudents && minStudents > maxStudents) {
        alert('El número mínimo de estudiantes no puede ser mayor al máximo');
        document.getElementById('min_students').value = maxStudents;
    }
}
</script>
@endsection