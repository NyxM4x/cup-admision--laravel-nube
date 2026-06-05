<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 'horarios';

    protected $fillable = [
        'codigo',
        'turno',
        'dias',
        'hora_inicio',
        'hora_fin',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }

    public function getRangoAttribute(): string
    {
        return substr($this->hora_inicio, 0, 5).' - '.substr($this->hora_fin, 0, 5);
    }
}
