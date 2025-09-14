<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Determinar si el usuario puede ver cualquier curso
     */
    public function viewAny(User $user): bool
    {
        return true; // Todos pueden ver la lista de cursos
    }

    /**
     * Determinar si el usuario puede ver el curso
     */
    public function view(User $user, Course $course): bool
    {
        return true; // Todos pueden ver cursos individuales
    }

    /**
     * Determinar si el usuario puede crear cursos
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'docente']);
    }

    /**
     * Determinar si el usuario puede actualizar el curso
     */
    public function update(User $user, Course $course): bool
    {
        return $user->hasRole('admin') || 
               ($user->hasRole('docente') && $course->instructor_id === $user->id);
    }

    /**
     * Determinar si el usuario puede eliminar el curso
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->hasRole('admin') || 
               ($user->hasRole('docente') && $course->instructor_id === $user->id);
    }

    /**
     * Determinar si el usuario puede inscribirse al curso
     */
    public function enroll(User $user, Course $course): bool
    {
        return $user->hasAnyRole(['alumno', 'anfitrion']);
    }
}