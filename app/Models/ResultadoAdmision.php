<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultadoAdmision extends Model
{
    protected $table = 'resultados_admision';

    protected $fillable = [
        'postulante_id',
        'periodo_id',
        'promedio_final',
        'posicion_ranking_general',
        'carrera_asignada_id',
        'estado_admision',
        'fecha_asignacion',
        'observacion',
    ];

    protected $casts = [
        'promedio_final'   => 'decimal:2',
        'fecha_asignacion' => 'datetime',
    ];

    public function postulante()
    {
        return $this->belongsTo(Postulante::class);
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    public function carreraAsignada()
    {
        return $this->belongsTo(Carrera::class, 'carrera_asignada_id');
    }

    public function getAprobadoAttribute(): bool
    {
        return $this->promedio_final >= 51;
    }
}
