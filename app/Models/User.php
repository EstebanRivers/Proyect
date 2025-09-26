<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Progress;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Progress[] $progress
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'carrera',
        'matricula',
        'semestre',
        'telefono',
        'curp',
        'fecha_nacimiento',
        'edad',
        'colonia',
        'calle',
        'ciudad',
        'estado',
        'codigo_postal',
        'foto_perfil',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Los roles del usuario
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole(string $roleName): bool
    {
        //dd($this->roles->pluck('name'));

        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Verificar si el usuario tiene alguno de los roles especificados
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', values: $roles)->exists();
    }

    /**
     * Asignar un rol al usuario
     */
    public function assignRole(string $roleName): void
    {
        $role = Role::where('name', $roleName)->first();
        if ($role && !$this->hasRole($roleName)) {
            $this->roles()->attach($role->id);
        }
    }

    /**
     * Remover un rol del usuario
     */
    public function removeRole(string $roleName): void
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->roles()->detach($role->id);
        }
    }

    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class, 'user_id');
    }

    /**
     * Obtener nombres de roles como array
     */
    public function getRoleNames(): array
    {
        return $this->roles->pluck('name')->toArray();
    }

    /**
     * Cursos en los que está inscrito el usuario
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_enrollments')
                    ->withPivot('status', 'grade', 'enrolled_at', 'completed_at')
                    ->withTimestamps();
    }

    /**
     * Verificar si el usuario está inscrito en un curso
     */
    public function isEnrolledIn(Course $course): bool
    {
        return $this->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'inscrito')
            ->exists();
    }

    /**
     * Verificar si el usuario completó un curso
     */
    public function hasCompleted(Course $course): bool
    {
        return $this->enrollments()
            ->where('course_id', $course->id)
            ->where('status', 'completado')
            ->exists();
    }
}
