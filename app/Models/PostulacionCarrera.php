<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostulacionCarrera extends Model
{
    protected $table = 'postulacion_carreras';

    protected $fillable = [
        'inscripcion_id',
        'carrera_id',
        'prioridad',
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }
}