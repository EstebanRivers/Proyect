<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentActivityResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'user_id',
        'responses',
        'score',
        'attempt_number',
        'started_at',
        'completed_at',
        'feedback',
    ];

    protected $casts = [
        'responses' => 'array',
        'score' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Actividad a la que pertenece la respuesta
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(TopicActivity::class, 'activity_id');
    }

    /**
     * Usuario que respondió
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verificar si está completada
     */
    public function isCompleted(): bool
    {
        return !is_null($this->completed_at);
    }

    /**
     * Obtener porcentaje de calificación
     */
    public function getScorePercentageAttribute(): ?float
    {
        if (!$this->score || !$this->activity->max_score) {
            return null;
        }

        return ($this->score / $this->activity->max_score) * 100;
    }

    /**
     * Verificar si aprobó
     */
    public function isPassed(): bool
    {
        return $this->score_percentage >= 70; // 70% mínimo para aprobar
    }
}