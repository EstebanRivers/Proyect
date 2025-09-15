<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Curso al que pertenece el tema
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Contenidos del tema
     */
    public function contents(): HasMany
    {
        return $this->hasMany(TopicContent::class, 'topic_id')->orderBy('order');
    }

    /**
     * Actividades del tema
     */
    public function activities(): HasMany
    {
        return $this->hasMany(TopicActivity::class, 'topic_id');
    }

    /**
     * Progreso de estudiantes en este tema
     */
    public function studentProgress(): HasMany
    {
        return $this->hasMany(StudentTopicProgress::class, 'topic_id');
    }

    /**
     * Verificar si un usuario completó este tema
     */
    public function isCompletedByUser(User $user): bool
    {
        $progress = $this->studentProgress()
            ->where('user_id', $user->id)
            ->first();

        return $progress && $progress->content_completed && $progress->activities_completed;
    }

    /**
     * Obtener duración total estimada del tema
     */

}