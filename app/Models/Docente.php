<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $table = 'docentes';

    protected $fillable = [
        'persona_id',
        'profesion_id',
        'profesion',
        'materia',          // sigla de la materia que dicta (ej: 'MAT', 'FIS')
        'anios_experiencia',
        'certif_docente',
        'certif_profesional',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // ── Relaciones ───────────────────────────────────────────

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function profesion()
    {
        return $this->belongsTo(Profesion::class);
    }

    // ── Scope: filtrar por sigla de materia ──────────────────

    /**
     * Scope para obtener docentes de una materia específica.
     * Uso: Docente::deMateria('MAT')->get()
     */
    public function scopeDeMateria($query, string $sigla)
    {
        return $query->where('materia', $sigla);
    }

    /**
     * Scope activos.
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // ── Accessor ─────────────────────────────────────────────

    public function getNombreCompletoAttribute(): string
    {
        return $this->persona?->nombre ?? 'Docente #' . $this->id;
    }
}