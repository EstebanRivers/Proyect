<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TopicContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'title',
        'description',
        'type',
        'file_path',
        'content',
        'order',
        'duration_minutes',
    ];

    /**
     * Tema al que pertenece el contenido
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(CourseTopic::class, 'topic_id');
    }

    /**
     * Obtener URL del archivo si existe
     */
    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    /**
     * Verificar si es contenido multimedia
     */
    public function isMultimedia(): bool
    {
        return in_array($this->type, ['video', 'presentation']);
    }

    /**
     * Obtener icono según el tipo de contenido
     */
    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'video' => '🎥',
            'document' => '📄',
            'presentation' => '📊',
            'text' => '📝',
            default => '📄'
        };
    }
}