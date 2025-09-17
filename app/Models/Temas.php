<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Temas extends Model
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
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class ,'curso_id');
    }

    /**
     * Actividades del tema
     */
    public function actividades(): HasMany
    {
        return $this->hasMany(Actividades::class, 'tema_id');
    }
}
