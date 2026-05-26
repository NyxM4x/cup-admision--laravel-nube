<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $table = 'docentes';

    protected $fillable = [
        'persona_id',
        'profesion_id',
        'anios_experiencia',
        'certif_docente',
        'certif_profesional',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relación con Persona (datos generales)
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    // Relación con Profesion
    public function profesion()
    {
        return $this->belongsTo(Profesion::class);
    }
}