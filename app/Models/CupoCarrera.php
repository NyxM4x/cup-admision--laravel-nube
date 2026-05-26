<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CupoCarrera extends Model
{
    protected $fillable = [
        'carrera_id',
        'periodo_id',
        'cupo_max',
        'fecha_cofi',
    ];

    protected $casts = [
        'fecha_cofi' => 'date',
    ];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }
}