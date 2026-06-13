<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Bloque de materia dentro de un grupo-turno.
 * Un grupo tiene exactamente 4 registros en esta tabla (MAT, FIS, ING, COMP).
 */
class GrupoMateria extends Model
{
    protected $table = 'grupo_materias';

    protected $fillable = [
        'grupo_id',
        'materia_id',
        'docente_id',
        'hora_inicio',
        'hora_fin',
        'aula_id',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    // ── Relaciones ───────────────────────────────────────────

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class);
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }

    public function notas()
    {
        return $this->hasMany(Nota::class);
    }

    // ── Accessors ────────────────────────────────────────────

    /** Devuelve "07:00 - 08:30" o "—" si no hay horario */
    public function getRangoAttribute(): string
    {
        if (! $this->hora_inicio || ! $this->hora_fin) {
            return '—';
        }
        return substr($this->hora_inicio, 0, 5) . ' - ' . substr($this->hora_fin, 0, 5);
    }

    /**
     * Aula efectiva: la específica de este bloque, o la del grupo padre si no tiene.
     */
    public function aulaEfectiva(): ?Aula
    {
        return $this->aula ?? $this->grupo?->aula;
    }
}