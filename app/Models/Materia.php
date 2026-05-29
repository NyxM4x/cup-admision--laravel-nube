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
        'dias_dictado',
        'hora_inicio',
        'hora_fin',
    ];

    protected $casts = [
        'activo'        => 'boolean',
        'peso_examen1'  => 'float',
        'peso_examen2'  => 'float',
        'peso_examen3'  => 'float',
        'dias_dictado'  => 'array',
    ];

    // Valida que los 3 pesos sumen exactamente 100
    public function pesosValidos(): bool
    {
        $suma = $this->peso_examen1 + $this->peso_examen2 + $this->peso_examen3;
        return abs($suma - 100) < 0.01;
    }

    public function getDiasFormateadosAttribute(): string
    {
        if (! $this->dias_dictado) {
            return '—';
        }
        $mapa = [
            'lunes' => 'Lun', 'martes' => 'Mar', 'miercoles' => 'Mié',
            'jueves' => 'Jue', 'viernes' => 'Vie', 'sabado' => 'Sáb',
        ];

        return collect($this->dias_dictado)
            ->map(fn ($d) => $mapa[$d] ?? ucfirst($d))
            ->join(', ');
    }

    public function getHorarioFormateadoAttribute(): string
    {
        if (! $this->hora_inicio || ! $this->hora_fin) {
            return '—';
        }

        return substr($this->hora_inicio, 0, 5).' - '.substr($this->hora_fin, 0, 5);
    }
}