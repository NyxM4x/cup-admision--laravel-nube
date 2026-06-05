<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'grupos';

    protected $fillable = [
        'codigo',
        'periodo_id',
        'materia_id',
        'horario_id',
        'aula_id',
        'docente_id',
        'cupo_max',
        'inscritos_actuales',
        'activo',
    ];

    protected $casts = [
        'activo'             => 'boolean',
        'cupo_max'           => 'integer',
        'inscritos_actuales' => 'integer',
    ];

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }

    public function postulantes()
    {
        return $this->belongsToMany(Postulante::class, 'grupo_postulante')
            ->withPivot('fecha_asignacion')
            ->withTimestamps();
    }
}
