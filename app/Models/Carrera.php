<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relación: una carrera tiene muchos cupos (uno por periodo)
    public function cupos()
    {
        return $this->hasMany(CupoCarrera::class);
    }

    // Cupo del periodo activo
    public function cupoActivo()
    {
        return $this->hasOne(CupoCarrera::class)
            ->whereHas('periodo', fn($q) => $q->where('activo', true));
    }
}