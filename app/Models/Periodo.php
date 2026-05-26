<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    protected $fillable = [
        'fecha_ini_inscripcion',
        'fecha_fin_inscripcion',
        'fecha_ini_curso',
        'fecha_fin_curso',
        'activo',
    ];

    protected $casts = [
        'fecha_ini_inscripcion' => 'date',
        'fecha_fin_inscripcion' => 'date',
        'fecha_ini_curso'       => 'date',
        'fecha_fin_curso'       => 'date',
        'activo'                => 'boolean',
    ];
}