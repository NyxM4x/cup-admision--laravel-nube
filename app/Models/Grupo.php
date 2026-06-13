<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Un Grupo representa un TURNO completo del CUP (Mañana o Tarde).
 * Las 4 materias con sus docentes y horarios específicos
 * viven en la tabla grupo_materias.
 */
class Grupo extends Model
{
    protected $table = 'grupos';

    protected $fillable = [
        'codigo',
        'periodo_id',
        'horario_id',   // Turno general (Mañana 07-12 / Tarde 13-18)
        'aula_id',      // Aula por defecto (puede ser sobreescrita por bloque)
        'cupo_max',
        'inscritos_actuales',
        'activo',
    ];

    protected $casts = [
        'activo'             => 'boolean',
        'cupo_max'           => 'integer',
        'inscritos_actuales' => 'integer',
    ];

    // ── Relaciones ───────────────────────────────────────────

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }

    /**
     * Los 4 bloques de materias del turno, ordenados por hora de inicio.
     */
    public function grupoMaterias()
    {
        return $this->hasMany(GrupoMateria::class);
    }

    /**
     * Postulantes inscritos en este grupo-turno (pivot grupo_postulante).
     */
    public function postulantes()
    {
        return $this->belongsToMany(Postulante::class, 'grupo_postulante')
            ->withPivot('fecha_asignacion')
            ->withTimestamps();
    }

    /**
     * CU21/CU22: Notas a través de grupo_materias.
     */
    public function notas()
    {
        return $this->hasManyThrough(Nota::class, GrupoMateria::class);
    }

    // ── Helpers ──────────────────────────────────────────────

    /** ¿Tiene cupo disponible? */
    public function tieneCupo(): bool
    {
        return $this->inscritos_actuales < $this->cupo_max;
    }

    /** Nombre legible del turno */
    public function getTurnoLabelAttribute(): string
    {
        return $this->horario?->turno ?? 'Sin turno';
    }

    /** ¿El grupo tiene sus 4 materias configuradas? */
    public function estaCompleto(): bool
    {
        return $this->grupoMaterias()->count() >= 4;
    }
}