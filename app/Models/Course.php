<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';

    protected $fillable = [
        'title',
        'description',
        'credits',
        'hours',
        'prerequisites',
        'instructor_id',
        'image',

    ];
    protected $casts = [
        'prerequisites' => 'array', // <-- AÑADE ESTA PROPIEDAD Y ESTA LÍNEA
    ];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topics::class, 'curso_id');
    }
}
