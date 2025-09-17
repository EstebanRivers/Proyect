<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topics extends Model
{
    use HasFactory;

    protected $fillable = [
        'curso_id',
        'title',
        'description',
    ];

    /**
     * Curso al que pertenece el tema
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class ,'curso_id');
    }

    /**
     * Actividades del tema
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activities::class, 'tema_id');
    }
}
