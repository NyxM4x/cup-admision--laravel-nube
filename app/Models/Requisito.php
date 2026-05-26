<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requisito extends Model
{
    protected $fillable = [
        'periodo_id',
        'nombre',
        'descripcion',
        'obligatorio',
        'formato_aceptado',
        'tamanio_max_kb',
        'activo',
    ];

    protected $casts = [
        'obligatorio' => 'boolean',
        'activo'      => 'boolean',
    ];

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }
}