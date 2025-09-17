<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Activities extends Model
{
    use HasFactory;

    protected $fillable = [
        'tema_id',
        'title',
        'description',
        'type',
        
    ];

    /**
     * Tema al que pertenece la actividad
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topics::class, 'tema_id');
    }
}
