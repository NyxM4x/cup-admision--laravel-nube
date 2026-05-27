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
    ];

    protected $casts = [
        'fecha_subida' => 'datetime',
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