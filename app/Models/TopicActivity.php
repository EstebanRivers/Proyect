<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TopicActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'title',
        'description',
        'type',
        'content',
        'max_attempts',
        'time_limit_minutes',
        'max_score',
        'is_required',
    ];

    protected $casts = [
        'content' => 'array',
        'is_required' => 'boolean',
        'max_score' => 'decimal:2',
    ];

    /**
     * Tema al que pertenece la actividad
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(CourseTopic::class, 'topic_id');
    }

    /**
     * Respuestas de estudiantes
     */
    public function responses(): HasMany
    {
        return $this->hasMany(StudentActivityResponse::class, 'activity_id');
    }

    /**
     * Obtener respuesta de un usuario específico
     */
    public function getResponseByUser(User $user): ?StudentActivityResponse
    {
        return $this->responses()
            ->where('user_id', $user->id)
            ->orderBy('attempt_number', 'desc')
            ->first();
    }

    /**
     * Verificar si un usuario completó la actividad
     */
    public function isCompletedByUser(User $user): bool
    {
        $response = $this->getResponseByUser($user);
        return $response && $response->completed_at;
    }

    /**
     * Obtener nombre del tipo de actividad
     */
    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            'quiz_multiple' => 'Cuestionario de Opción Múltiple',
            'quiz_open' => 'Cuestionario de Respuesta Abierta',
            'essay' => 'Ensayo',
            'assignment' => 'Tarea',
            default => 'Actividad'
        };
    }

    /**
     * Obtener icono del tipo de actividad
     */
    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'quiz_multiple' => '✅',
            'quiz_open' => '❓',
            'essay' => '📝',
            'assignment' => '📋',
            default => '📝'
        };
    }
}