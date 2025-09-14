<!-- Contenido de un tema específico -->
<div style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
    <!-- Header del tema -->
    <div style="border-bottom: 2px solid #e69a37; padding-bottom: 20px; margin-bottom: 30px;">
        <h2 style="margin: 0 0 8px 0; color: #333; font-size: 28px; font-weight: 600;">
            {{ $topic->title }}
        </h2>
        @if($topic->description)
            <p style="margin: 0; color: #666; font-size: 16px; line-height: 1.5;">
                {{ $topic->description }}
            </p>
        @endif
    </div>
    
    <!-- Contenidos del tema -->
    @if($topic->contents->count() > 0)
        <div style="margin-bottom: 40px;">
            <h3 style="margin: 0 0 20px 0; color: #333; font-size: 20px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                📚 Contenidos de Estudio
            </h3>
            
            <div style="display: grid; gap: 16px;">
                @foreach($topic->contents as $content)
                    <div style="border: 1px solid #eee; border-radius: 12px; padding: 20px; transition: all 0.2s ease;" 
                         onmouseover="this.style.borderColor='#e69a37'; this.style.background='#fef9f3';"
                         onmouseout="this.style.borderColor='#eee'; this.style.background='white';">
                        
                        <div style="display: flex; align-items: flex-start; gap: 16px;">
                            <!-- Icono del tipo de contenido -->
                            <div style="width: 48px; height: 48px; border-radius: 12px; background: #e69a37; color: white; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                                {{ $content->type_icon }}
                            </div>
                            
                            <div style="flex: 1;">
                                <h4 style="margin: 0 0 8px 0; color: #333; font-size: 16px; font-weight: 600;">
                                    {{ $content->title }}
                                </h4>
                                
                                @if($content->description)
                                    <p style="margin: 0 0 12px 0; color: #666; font-size: 14px; line-height: 1.4;">
                                        {{ $content->description }}
                                    </p>
                                @endif
                                
                                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 12px;">
                                    <span style="background: #f8f9fa; color: #666; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 500; text-transform: capitalize;">
                                        {{ str_replace('_', ' ', $content->type) }}
                                    </span>
                                    @if($content->duration_minutes)
                                        <span style="color: #666; font-size: 12px;">
                                            ⏱️ {{ $content->duration_minutes }} min
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Contenido específico según el tipo -->
                                @if($content->type === 'text' && $content->content)
                                    <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; margin-top: 12px;">
                                        <div style="color: #333; line-height: 1.6;">
                                            {!! nl2br(e($content->content)) !!}
                                        </div>
                                    </div>
                                @elseif($content->file_path)
                                    <div style="margin-top: 12px;">
                                        @if($content->type === 'video')
                                            <div style="background: #000; border-radius: 8px; aspect-ratio: 16/9; display: flex; align-items: center; justify-content: center; color: white;">
                                                <div style="text-align: center;">
                                                    <div style="font-size: 48px; margin-bottom: 8px;">🎥</div>
                                                    <div>Video no disponible</div>
                                                    <div style="font-size: 12px; opacity: 0.7;">{{ $content->file_path }}</div>
                                                </div>
                                            </div>
                                        @elseif($content->type === 'document')
                                            <a href="{{ $content->file_url }}" target="_blank" 
                                               style="display: inline-flex; align-items: center; gap: 8px; background: #e69a37; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 500;">
                                                📄 Abrir Documento
                                            </a>
                                        @elseif($content->type === 'presentation')
                                            <a href="{{ $content->file_url }}" target="_blank"
                                               style="display: inline-flex; align-items: center; gap: 8px; background: #e69a37; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 500;">
                                                📊 Ver Presentación
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; margin-top: 12px; text-align: center; color: #666;">
                                        <div style="font-size: 24px; margin-bottom: 8px;">{{ $content->type_icon }}</div>
                                        <div>Contenido en preparación</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Actividades del tema -->
    @if($topic->activities->count() > 0)
        <div style="margin-bottom: 40px;">
            <h3 style="margin: 0 0 20px 0; color: #333; font-size: 20px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                ✏️ Actividades y Evaluaciones
            </h3>
            
            <div style="display: grid; gap: 16px;">
                @foreach($topic->activities as $activity)
                    @php
                        $userResponse = $activity->getResponseByUser(Auth::user());
                        $isCompleted = $activity->isCompletedByUser(Auth::user());
                    @endphp
                    
                    <div style="border: 1px solid #eee; border-radius: 12px; padding: 20px; {{ $isCompleted ? 'background: #f0f9ff; border-color: #0ea5e9;' : '' }}">
                        <div style="display: flex; align-items: flex-start; gap: 16px;">
                            <!-- Icono de la actividad -->
                            <div style="width: 48px; height: 48px; border-radius: 12px; background: {{ $isCompleted ? '#0ea5e9' : '#e69a37' }}; color: white; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                                {{ $activity->type_icon }}
                            </div>
                            
                            <div style="flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                    <h4 style="margin: 0; color: #333; font-size: 16px; font-weight: 600;">
                                        {{ $activity->title }}
                                    </h4>
                                    @if($isCompleted)
                                        <span style="background: #0ea5e9; color: white; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                            ✓ Completada
                                        </span>
                                    @endif
                                </div>
                                
                                @if($activity->description)
                                    <p style="margin: 0 0 12px 0; color: #666; font-size: 14px; line-height: 1.4;">
                                        {{ $activity->description }}
                                    </p>
                                @endif
                                
                                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px; flex-wrap: wrap;">
                                    <span style="background: #f8f9fa; color: #666; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                        {{ $activity->type_name }}
                                    </span>
                                    <span style="color: #666; font-size: 12px;">
                                        🎯 {{ $activity->max_score }} puntos
                                    </span>
                                    @if($activity->time_limit_minutes)
                                        <span style="color: #666; font-size: 12px;">
                                            ⏱️ {{ $activity->time_limit_minutes }} min
                                        </span>
                                    @endif
                                    <span style="color: #666; font-size: 12px;">
                                        🔄 {{ $activity->max_attempts }} intentos
                                    </span>
                                </div>
                                
                                <!-- Información de progreso si ya se intentó -->
                                @if($userResponse)
                                    <div style="background: #f8f9fa; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                            <span style="font-weight: 600; color: #333;">Tu Progreso</span>
                                            @if($userResponse->score !== null)
                                                <span style="color: {{ $userResponse->isPassed() ? '#28a745' : '#dc3545' }}; font-weight: 600;">
                                                    {{ $userResponse->score }}/{{ $activity->max_score }} 
                                                    ({{ number_format($userResponse->score_percentage, 1) }}%)
                                                </span>
                                            @endif
                                        </div>
                                        <div style="color: #666; font-size: 13px;">
                                            Intento {{ $userResponse->attempt_number }}/{{ $activity->max_attempts }}
                                            @if($userResponse->completed_at)
                                                • Completado el {{ $userResponse->completed_at->format('d/m/Y H:i') }}
                                            @endif
                                        </div>
                                        @if($userResponse->feedback)
                                            <div style="margin-top: 8px; padding: 8px; background: white; border-radius: 4px; border-left: 3px solid #e69a37;">
                                                <strong style="color: #333; font-size: 13px;">Retroalimentación:</strong>
                                                <div style="color: #666; font-size: 13px; margin-top: 4px;">{{ $userResponse->feedback }}</div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                
                                <!-- Botones de acción -->
                                <div style="display: flex; gap: 12px;">
                                    @if(!$isCompleted && (!$userResponse || $userResponse->attempt_number < $activity->max_attempts))
                                        <button onclick="startActivity({{ $activity->id }})"
                                                style="background: #e69a37; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                                            {{ $userResponse ? 'Reintentar' : 'Comenzar' }}
                                        </button>
                                    @endif
                                    
                                    @if($userResponse && $userResponse->completed_at)
                                        <button onclick="viewActivityResults({{ $activity->id }})"
                                                style="background: #17a2b8; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                                            Ver Resultados
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Botón para marcar tema como completado -->
    @if(!$topic->isCompletedByUser(Auth::user()))
        <div style="text-align: center; padding-top: 30px; border-top: 1px solid #eee;">
            <button onclick="markTopicAsCompleted({{ $topic->id }})"
                    style="background: #28a745; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 16px;">
                ✓ Marcar Tema como Completado
            </button>
            <p style="margin: 8px 0 0 0; color: #666; font-size: 14px;">
                Marca este tema como completado cuando hayas revisado todo el contenido
            </p>
        </div>
    @else
        <div style="text-align: center; padding-top: 30px; border-top: 1px solid #eee;">
            <div style="background: #d4edda; color: #155724; padding: 12px 24px; border-radius: 8px; display: inline-block;">
                <strong>✓ Tema Completado</strong>
            </div>
        </div>
    @endif
</div>

<script>
function startActivity(activityId) {
    // Aquí implementaremos la lógica para iniciar actividades
    alert('Funcionalidad de actividades en desarrollo. ID: ' + activityId);
}

function viewActivityResults(activityId) {
    // Aquí implementaremos la vista de resultados
    alert('Ver resultados de actividad. ID: ' + activityId);
}

function markTopicAsCompleted(topicId) {
    if (!confirm('¿Estás seguro de que has completado este tema?')) {
        return;
    }
    
    fetch(`/courses/topics/${topicId}/complete`, {
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
        alert('Error al marcar el tema como completado');
    });
}
</script>