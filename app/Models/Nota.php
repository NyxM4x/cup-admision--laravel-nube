<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CU21/CU22 — Notas de exámenes del CUP.
 * Un postulante tiene una nota por cada GrupoMateria (bloque) de su grupo-turno.
 */
class Nota extends Model
{
    protected $table = 'notas';

    protected $fillable = [
        'grupo_materia_id',
        'postulante_id',
        'examen1',
        'examen2',
        'examen3',
        'nota_final',
        'resultado',
        'registrado_por',
        'observacion',
    ];

    protected $casts = [
        'examen1'    => 'decimal:2',
        'examen2'    => 'decimal:2',
        'examen3'    => 'decimal:2',
        'nota_final' => 'decimal:2',
    ];

    // ── Constante de aprobación ───────────────────────────
    public const NOTA_MINIMA = 51;

    // ── Relaciones ────────────────────────────────────────

    public function grupoMateria()
    {
        return $this->belongsTo(GrupoMateria::class);
    }

    public function postulante()
    {
        return $this->belongsTo(Postulante::class);
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    // ── CU22: Calcular nota final y resultado ─────────────

    /**
     * Calcula la nota final como promedio de los 3 exámenes
     * y determina si APROBADO o REPROBADO.
     * Guarda automáticamente.
     */
    public function calcularYGuardar(): void
    {
        $notas = collect([$this->examen1, $this->examen2, $this->examen3])
            ->filter(fn ($n) => ! is_null($n));

        if ($notas->isEmpty()) {
            $this->nota_final = null;
            $this->resultado  = 'pendiente';
        } else {
            $promedio        = round($notas->avg(), 2);
            $this->nota_final = $promedio;
            $this->resultado  = $promedio >= self::NOTA_MINIMA ? 'aprobado' : 'reprobado';
        }

        $this->save();
    }

    // ── Accessors ─────────────────────────────────────────

    public function getNotaFormateadaAttribute(): string
    {
        return $this->nota_final !== null
            ? number_format($this->nota_final, 2)
            : '—';
    }

    public function getBadgeResultadoAttribute(): string
    {
        return match ($this->resultado) {
            'aprobado'  => '<span class="badge bg-success">Aprobado</span>',
            'reprobado' => '<span class="badge bg-danger">Reprobado</span>',
            default     => '<span class="badge bg-secondary">Pendiente</span>',
        };
    }
}