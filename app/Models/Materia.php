<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $fillable = [
        'sigla',
        'nombre',
        'dias',
        'cant_examenes',
        'peso_examen1',
        'peso_examen2',
        'peso_examen3',
        'activo',
    ];

    protected $casts = [
        'activo'        => 'boolean',
        'peso_examen1'  => 'float',
        'peso_examen2'  => 'float',
        'peso_examen3'  => 'float',
    ];

    // Valida que los 3 pesos sumen exactamente 100
    public function pesosValidos(): bool
    {
        $suma = $this->peso_examen1 + $this->peso_examen2 + $this->peso_examen3;
        return abs($suma - 100) < 0.01;
    }
}