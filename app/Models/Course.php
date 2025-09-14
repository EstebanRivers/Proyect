<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'short_description',
        'code',
        'credits',
        'duration_hours',
        'difficulty',
        'status',
        'image',
        'max_students',
        'min_students',
        'start_date',
        'end_date',
        'price',
        'instructor_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
    ];

    /**
     * Instructor del curso
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Estudiantes inscritos
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    /**
     * Estudiantes inscritos (relación directa)
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_enrollments')
                    ->withPivot('status', 'grade', 'enrolled_at', 'completed_at')
                    ->withTimestamps();
    }

    /**
     * Prerrequisitos del curso
     */
    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_prerequisites', 'course_id', 'prerequisite_course_id')
                    ->withTimestamps();
    }

    /**
     * Cursos que tienen este como prerrequisito
     */
    public function dependentCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_prerequisites', 'prerequisite_course_id', 'course_id')
                    ->withTimestamps();
    }

    /**
     * Verificar si el usuario puede inscribirse al curso
     */
    public function canUserEnroll(User $user): bool
    {
        // Verificar si ya está inscrito
        if ($this->students()->where('user_id', $user->id)->exists()) {
            return false;
        }

        // Verificar prerrequisitos
        foreach ($this->prerequisites as $prerequisite) {
            $enrollment = $prerequisite->enrollments()
                ->where('user_id', $user->id)
                ->where('status', 'completado')
                ->first();
            
            if (!$enrollment) {
                return false;
            }
        }

        // Verificar capacidad máxima
        if ($this->enrollments()->where('status', 'inscrito')->count() >= $this->max_students) {
            return false;
        }

        // Verificar que el curso esté activo
        return $this->status === 'activo';
    }

    /**
     * Obtener prerrequisitos faltantes para un usuario
     */
    public function getMissingPrerequisites(User $user): array
    {
        $missing = [];
        
        foreach ($this->prerequisites as $prerequisite) {
            $enrollment = $prerequisite->enrollments()
                ->where('user_id', $user->id)
                ->where('status', 'completado')
                ->first();
            
            if (!$enrollment) {
                $missing[] = $prerequisite;
            }
        }
        
        return $missing;
    }

    /**
     * Obtener número de estudiantes inscritos
     */
    public function getEnrolledCountAttribute(): int
    {
        return $this->enrollments()->where('status', 'inscrito')->count();
    }

    /**
     * Verificar si el curso está lleno
     */
    public function getIsFullAttribute(): bool
    {
        return $this->enrolled_count >= $this->max_students;
    }

    /**
     * Obtener el progreso del curso (porcentaje de cupos ocupados)
     */
    public function getProgressAttribute(): int
    {
        if ($this->max_students == 0) return 0;
        return min(100, round(($this->enrolled_count / $this->max_students) * 100));
    }
}