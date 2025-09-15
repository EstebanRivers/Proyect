@extends('layouts.app')

@section('title', 'Crear Temas - ' . $course->title)

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
    <!-- Header -->
    <div style="background: white; border-radius: 16px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h1 style="color: #333; margin: 0 0 8px 0; font-size: 28px; font-weight: 600;">Crear Contenido del Curso</h1>
                <p style="color: #666; margin: 0; font-size: 16px;">{{ $course->title }}</p>
            </div>
            <div style="display: flex; gap: 12px;">
                <button onclick="saveCourse()" 
                        style="background: #28a745; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 16px;">
                    ✓ Finalizar Curso
                </button>
                <button onclick="window.navigateTo('{{ route('courses.index') }}')"
                        style="background: #6c757d; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                    Cancelar
                </button>
            </div>
        </div>
        
        <!-- Progreso -->
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
            <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 8px;">
                <span style="font-weight: 600; color: #333;">Progreso del Curso</span>
                <span id="progress-text" style="color: #666; font-size: 14px;">0 temas creados</span>
            </div>
            <div style="background: #e9ecef; height: 8px; border-radius: 4px; overflow: hidden;">
                <div id="progress-bar" style="background: #e69a37; height: 100%; width: 0%; transition: width 0.3s ease;"></div>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 300px 1fr; gap: 30px;">
        <!-- Sidebar - Lista de temas -->
        <div style="background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); height: fit-content; position: sticky; top: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0; color: #333; font-size: 18px; font-weight: 600;">Temas del Curso</h3>
                <button onclick="addNewTopic()" 
                        style="background: #e69a37; color: white; padding: 8px 12px; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 14px;">
                    + Agregar
                </button>
            </div>
            
            <div id="topics-list" style="space-y: 8px;">
                <!-- Los temas se agregarán dinámicamente aquí -->
            </div>
            
            <div id="empty-state" style="text-align: center; padding: 40px 20px; color: #666;">
                <div style="font-size: 48px; margin-bottom: 16px;">📚</div>
                <p style="margin: 0; font-size: 14px;">No hay temas creados aún.<br>Haz clic en "Agregar" para comenzar.</p>
            </div>
        </div>
        
        <!-- Editor principal -->
        <div id="topic-editor" style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); min-height: 600px;">
            <div id="welcome-state" style="text-align: center; padding: 80px 20px; color: #666;">
                <div style="font-size: 64px; margin-bottom: 20px;">✏️</div>
                <h3 style="margin: 0 0 12px 0; color: #333;">Editor de Temas</h3>
                <p style="margin: 0;">Selecciona un tema de la lista o crea uno nuevo para comenzar a editarlo.</p>
            </div>
        </div>
    </div>
</div>

<script>
let topics = [];
let currentTopicIndex = -1;
let topicCounter = 0;

// Agregar nuevo tema
function addNewTopic() {
    topicCounter++;
    const newTopic = {
        id: Date.now(),
        title: `Tema ${topicCounter}`,
        description: '',
        contents: [],
        activities: [],
        order: topics.length + 1
    };
    
    topics.push(newTopic);
    renderTopicsList();
    editTopic(topics.length - 1);
    updateProgress();
}

// Renderizar lista de temas
function renderTopicsList() {
    const topicsList = document.getElementById('topics-list');
    const emptyState = document.getElementById('empty-state');
    
    if (topics.length === 0) {
        topicsList.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }
    
    emptyState.style.display = 'none';
    
    topicsList.innerHTML = topics.map((topic, index) => `
        <div style="border-radius: 8px; overflow: hidden; ${currentTopicIndex === index ? 'background: #fef9f3; border: 2px solid #e69a37;' : 'background: #f8f9fa; border: 2px solid transparent;'}">
            <div style="display: flex; align-items: center; padding: 12px; gap: 12px;">
                <div style="width: 24px; height: 24px; border-radius: 50%; background: ${currentTopicIndex === index ? '#e69a37' : '#dee2e6'}; color: ${currentTopicIndex === index ? 'white' : '#6c757d'}; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">
                    ${index + 1}
                </div>
                <div style="flex: 1; cursor: pointer;" onclick="editTopic(${index})">
                    <div style="font-weight: 600; color: #333; font-size: 14px; margin-bottom: 2px;">
                        ${topic.title}
                    </div>
                    <div style="color: #666; font-size: 12px;">
                        ${topic.contents.length} contenidos • ${topic.activities.length} actividades
                    </div>
                </div>
                <button onclick="deleteTopic(${index})" 
                        style="background: #dc3545; color: white; border: none; border-radius: 4px; width: 24px; height: 24px; cursor: pointer; font-size: 12px;">
                    ×
                </button>
            </div>
        </div>
    `).join('');
}

// Editar tema
function editTopic(index) {
    currentTopicIndex = index;
    const topic = topics[index];
    
    document.getElementById('welcome-state').style.display = 'none';
    document.getElementById('topic-editor').innerHTML = `
        <div style="margin-bottom: 30px;">
            <h2 style="margin: 0 0 20px 0; color: #333; font-size: 24px; font-weight: 600;">Editando Tema ${index + 1}</h2>
            
            <!-- Información básica del tema -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 12px; margin-bottom: 30px;">
                <h4 style="margin: 0 0 15px 0; color: #333;">Información del Tema</h4>
                <div style="display: grid; gap: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">Título del Tema *</label>
                        <input type="text" id="topic-title" value="${topic.title}" 
                               onchange="updateTopicTitle(${index}, this.value)"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #555;">Descripción</label>
                        <textarea id="topic-description" rows="3" 
                                  onchange="updateTopicDescription(${index}, this.value)"
                                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; resize: vertical;">${topic.description}</textarea>
                    </div>
                </div>
            </div>
            
            <!-- Contenidos -->
            <div style="margin-bottom: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h4 style="margin: 0; color: #333;">Contenidos del Tema</h4>
                    <div style="display: flex; gap: 8px;">
                        <button onclick="addContent(${index}, 'video')" 
                                style="background: #17a2b8; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                            + Video
                        </button>
                        <button onclick="addContent(${index}, 'document')" 
                                style="background: #28a745; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                            + Documento
                        </button>
                        <button onclick="addContent(${index}, 'presentation')" 
                                style="background: #ffc107; color: black; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                            + Presentación
                        </button>
                        <button onclick="addContent(${index}, 'text')" 
                                style="background: #6f42c1; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                            + Texto
                        </button>
                    </div>
                </div>
                <div id="contents-list-${index}">
                    ${renderContentsList(topic.contents, index)}
                </div>
            </div>
            
            <!-- Actividades -->
            <div style="margin-bottom: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h4 style="margin: 0; color: #333;">Actividades del Tema</h4>
                    <div style="display: flex; gap: 8px;">
                        <button onclick="addActivity(${index}, 'quiz_multiple')" 
                                style="background: #e69a37; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                            + Quiz Múltiple
                        </button>
                        <button onclick="addActivity(${index}, 'quiz_open')" 
                                style="background: #fd7e14; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                            + Quiz Abierto
                        </button>
                        <button onclick="addActivity(${index}, 'essay')" 
                                style="background: #6610f2; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                            + Ensayo
                        </button>
                        <button onclick="addActivity(${index}, 'assignment')" 
                                style="background: #20c997; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">
                            + Tarea
                        </button>
                    </div>
                </div>
                <div id="activities-list-${index}">
                    ${renderActivitiesList(topic.activities, index)}
                </div>
            </div>
        </div>
    `;
    
    renderTopicsList();
}

// Renderizar lista de contenidos
function renderContentsList(contents, topicIndex) {
    if (contents.length === 0) {
        return '<p style="color: #666; text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px; font-style: italic;">No hay contenidos agregados aún.</p>';
    }
    
    return contents.map((content, contentIndex) => `
        <div style="border: 1px solid #eee; border-radius: 8px; padding: 15px; margin-bottom: 10px; background: white;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                <div style="flex: 1;">
                    <input type="text" value="${content.title}" 
                           onchange="updateContentTitle(${topicIndex}, ${contentIndex}, this.value)"
                           style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-weight: 600; margin-bottom: 8px;">
                    <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 8px;">
                        <span style="background: #e69a37; color: white; padding: 2px 8px; border-radius: 4px; font-size: 11px; text-transform: capitalize;">
                            ${content.type.replace('_', ' ')}
                        </span>
                        <input type="number" value="${content.duration_minutes || 0}" min="0" max="180"
                               onchange="updateContentDuration(${topicIndex}, ${contentIndex}, this.value)"
                               style="width: 80px; padding: 4px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px;">
                        <span style="font-size: 12px; color: #666;">minutos</span>
                    </div>
                    <textarea rows="2" placeholder="Descripción del contenido..."
                              onchange="updateContentDescription(${topicIndex}, ${contentIndex}, this.value)"
                              style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; resize: vertical;">${content.description || ''}</textarea>
                </div>
                <button onclick="deleteContent(${topicIndex}, ${contentIndex})" 
                        style="background: #dc3545; color: white; border: none; border-radius: 4px; width: 24px; height: 24px; cursor: pointer; font-size: 12px; margin-left: 10px;">
                    ×
                </button>
            </div>
        </div>
    `).join('');
}

// Renderizar lista de actividades
function renderActivitiesList(activities, topicIndex) {
    if (activities.length === 0) {
        return '<p style="color: #666; text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px; font-style: italic;">No hay actividades agregadas aún.</p>';
    }
    
    return activities.map((activity, activityIndex) => `
        <div style="border: 1px solid #eee; border-radius: 8px; padding: 15px; margin-bottom: 10px; background: white;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                <div style="flex: 1;">
                    <input type="text" value="${activity.title}" 
                           onchange="updateActivityTitle(${topicIndex}, ${activityIndex}, this.value)"
                           style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-weight: 600; margin-bottom: 8px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 8px;">
                        <div>
                            <label style="font-size: 11px; color: #666; display: block; margin-bottom: 2px;">Tipo</label>
                            <span style="background: #e69a37; color: white; padding: 2px 8px; border-radius: 4px; font-size: 11px; text-transform: capitalize;">
                                ${activity.type.replace('_', ' ')}
                            </span>
                        </div>
                        <div>
                            <label style="font-size: 11px; color: #666; display: block; margin-bottom: 2px;">Puntos</label>
                            <input type="number" value="${activity.max_score || 100}" min="1" max="1000"
                                   onchange="updateActivityScore(${topicIndex}, ${activityIndex}, this.value)"
                                   style="width: 100%; padding: 4px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px;">
                        </div>
                        <div>
                            <label style="font-size: 11px; color: #666; display: block; margin-bottom: 2px;">Intentos</label>
                            <input type="number" value="${activity.max_attempts || 3}" min="1" max="10"
                                   onchange="updateActivityAttempts(${topicIndex}, ${activityIndex}, this.value)"
                                   style="width: 100%; padding: 4px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px;">
                        </div>
                    </div>
                    <textarea rows="2" placeholder="Descripción de la actividad..."
                              onchange="updateActivityDescription(${topicIndex}, ${activityIndex}, this.value)"
                              style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; resize: vertical;">${activity.description || ''}</textarea>
                </div>
                <button onclick="deleteActivity(${topicIndex}, ${activityIndex})" 
                        style="background: #dc3545; color: white; border: none; border-radius: 4px; width: 24px; height: 24px; cursor: pointer; font-size: 12px; margin-left: 10px;">
                    ×
                </button>
            </div>
        </div>
    `).join('');
}

// Funciones de actualización
function updateTopicTitle(index, value) {
    topics[index].title = value;
    renderTopicsList();
}

function updateTopicDescription(index, value) {
    topics[index].description = value;
}

// Funciones de contenido
function addContent(topicIndex, type) {
    const content = {
        id: Date.now(),
        title: `${type.charAt(0).toUpperCase() + type.slice(1)} - ${topics[topicIndex].title}`,
        description: '',
        type: type,
        duration_minutes: type === 'video' ? 15 : (type === 'presentation' ? 20 : 10),
        order: topics[topicIndex].contents.length + 1
    };
    
    topics[topicIndex].contents.push(content);
    document.getElementById(`contents-list-${topicIndex}`).innerHTML = renderContentsList(topics[topicIndex].contents, topicIndex);
    renderTopicsList();
}

function updateContentTitle(topicIndex, contentIndex, value) {
    topics[topicIndex].contents[contentIndex].title = value;
}

function updateContentDescription(topicIndex, contentIndex, value) {
    topics[topicIndex].contents[contentIndex].description = value;
}

function updateContentDuration(topicIndex, contentIndex, value) {
    topics[topicIndex].contents[contentIndex].duration_minutes = parseInt(value) || 0;
}

function deleteContent(topicIndex, contentIndex) {
    if (confirm('¿Estás seguro de que quieres eliminar este contenido?')) {
        topics[topicIndex].contents.splice(contentIndex, 1);
        document.getElementById(`contents-list-${topicIndex}`).innerHTML = renderContentsList(topics[topicIndex].contents, topicIndex);
        renderTopicsList();
    }
}

// Funciones de actividades
function addActivity(topicIndex, type) {
    const activity = {
        id: Date.now(),
        title: `${type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())} - ${topics[topicIndex].title}`,
        description: '',
        type: type,
        max_score: 100,
        max_attempts: 3,
        order: topics[topicIndex].activities.length + 1
    };
    
    topics[topicIndex].activities.push(activity);
    document.getElementById(`activities-list-${topicIndex}`).innerHTML = renderActivitiesList(topics[topicIndex].activities, topicIndex);
    renderTopicsList();
}

function updateActivityTitle(topicIndex, activityIndex, value) {
    topics[topicIndex].activities[activityIndex].title = value;
}

function updateActivityDescription(topicIndex, activityIndex, value) {
    topics[topicIndex].activities[activityIndex].description = value;
}

function updateActivityScore(topicIndex, activityIndex, value) {
    topics[topicIndex].activities[activityIndex].max_score = parseInt(value) || 100;
}

function updateActivityAttempts(topicIndex, activityIndex, value) {
    topics[topicIndex].activities[activityIndex].max_attempts = parseInt(value) || 3;
}

function deleteActivity(topicIndex, activityIndex) {
    if (confirm('¿Estás seguro de que quieres eliminar esta actividad?')) {
        topics[topicIndex].activities.splice(activityIndex, 1);
        document.getElementById(`activities-list-${topicIndex}`).innerHTML = renderActivitiesList(topics[topicIndex].activities, topicIndex);
        renderTopicsList();
    }
}

// Eliminar tema
function deleteTopic(index) {
    if (confirm('¿Estás seguro de que quieres eliminar este tema? Se perderá todo su contenido.')) {
        topics.splice(index, 1);
        
        // Reajustar índices
        if (currentTopicIndex === index) {
            currentTopicIndex = -1;
            document.getElementById('topic-editor').innerHTML = `
                <div id="welcome-state" style="text-align: center; padding: 80px 20px; color: #666;">
                    <div style="font-size: 64px; margin-bottom: 20px;">✏️</div>
                    <h3 style="margin: 0 0 12px 0; color: #333;">Editor de Temas</h3>
                    <p style="margin: 0;">Selecciona un tema de la lista o crea uno nuevo para comenzar a editarlo.</p>
                </div>
            `;
        } else if (currentTopicIndex > index) {
            currentTopicIndex--;
        }
        
        renderTopicsList();
        updateProgress();
    }
}

// Actualizar progreso
function updateProgress() {
    const progressText = document.getElementById('progress-text');
    const progressBar = document.getElementById('progress-bar');
    
    progressText.textContent = `${topics.length} tema${topics.length !== 1 ? 's' : ''} creado${topics.length !== 1 ? 's' : ''}`;
    
    // Calcular progreso basado en temas con contenido
    const topicsWithContent = topics.filter(topic => topic.contents.length > 0 || topic.activities.length > 0).length;
    const progress = topics.length > 0 ? (topicsWithContent / topics.length) * 100 : 0;
    progressBar.style.width = progress + '%';
}

// Guardar curso
function saveCourse() {
    if (topics.length === 0) {
        alert('Debes crear al menos un tema para el curso.');
        return;
    }
    
    const courseData = {
        course_id: {{ $course->id }},
        topics: topics
    };
    
    fetch('/courses/{{ $course->id }}/save-topics', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(courseData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Curso creado exitosamente!');
            window.navigateTo('/courses');
        } else {
            alert('Error al guardar el curso: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al guardar el curso');
    });
}

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    renderTopicsList();
    updateProgress();
});
</script>
@endsection