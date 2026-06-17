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
        'materia',          // sigla principal (compat. con GrupoController)
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

    public function docenteMaterias()
    {
        return $this->hasMany(DocenteMateria::class);
    }

    // ── Scopes ───────────────────────────────────────────────

    /** Filtrar por sigla en el campo denormalizado (usado por GrupoController). */
    public function scopeDeMateria($query, string $sigla)
    {
        return $query->where('materia', $sigla);
    }

    /** Filtrar por sigla en la tabla docente_materias (múltiples materias). */
    public function scopeDeMateriasMultiple($query, string $sigla)
    {
        return $query->whereHas('docenteMaterias', fn($q) => $q->where('materia_sigla', $sigla));
    }

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
