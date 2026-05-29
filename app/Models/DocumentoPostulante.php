<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoPostulante extends Model
{
    protected $table = 'documentos_postulantes';

    protected $fillable = [
        'inscripcion_id',
        'requisito_id',
        'archivo',
        'estado',
        'comentario',
        'fecha_subida',
        'cumplido',
        'fecha_validacion',
        'validado_por',
    ];

    protected $casts = [
        'fecha_subida'     => 'datetime',
        'cumplido'         => 'boolean',
        'fecha_validacion' => 'datetime',
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function requisito()
    {
        return $this->belongsTo(Requisito::class);
    }
}