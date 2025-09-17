<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';

    protected $fillable = [
        'title',
        'description',
        'credits',
        'hours',
        'prerequisites',
        'instructor_id',
        'image',

    ];
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function temas(): HasMany
    {
        return $this->hasMany(Temas::class, 'curso_id');
    }
}
