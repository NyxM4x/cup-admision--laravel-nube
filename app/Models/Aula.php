<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    protected $table = 'aulas';

    protected $fillable = [
        'codigo',
        'edificio',
        'capacidad',
        'equipamiento',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'capacidad' => 'integer',
    ];

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
