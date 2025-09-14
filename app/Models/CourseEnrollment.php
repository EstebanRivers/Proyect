<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'status',
        'grade',
        'enrolled_at',
        'completed_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'grade' => 'decimal:2',
    ];

    /**
     * Curso al que pertenece la inscripción
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Usuario inscrito
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Marcar como completado
     */
    public function markAsCompleted(float $grade = null): void
    {
        $this->update([
            'status' => 'completado',
            'grade' => $grade,
            'completed_at' => now(),
        ]);
    }

    /**
     * Verificar si está aprobado
     */
    public function isPassed(): bool
    {
        return $this->status === 'completado' && ($this->grade === null || $this->grade >= 70);
    }
}