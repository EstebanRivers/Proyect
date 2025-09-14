<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentTopicProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'topic_id',
        'content_completed',
        'activities_completed',
        'topic_score',
        'completed_at',
    ];

    protected $casts = [
        'content_completed' => 'boolean',
        'activities_completed' => 'boolean',
        'topic_score' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    /**
     * Usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tema
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(CourseTopic::class, 'topic_id');
    }

    /**
     * Verificar si el tema está completado
     */
    public function isCompleted(): bool
    {
        return $this->content_completed && $this->activities_completed;
    }

    /**
     * Marcar como completado
     */
    public function markAsCompleted(): void
    {
        if ($this->isCompleted() && !$this->completed_at) {
            $this->update(['completed_at' => now()]);
        }
    }
}