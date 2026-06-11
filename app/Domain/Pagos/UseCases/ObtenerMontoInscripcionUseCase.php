<?php

namespace App\Domain\Pagos\UseCases;

use App\Models\Inscripcion;
use App\Models\CupoCarrera;
use App\Models\Periodo;

class ObtenerMontoInscripcionUseCase
{
    public function ejecutar(Inscripcion $inscripcion): float
    {
        $periodoActivo = Periodo::where('activo', true)->first();

        if (!$periodoActivo) {
            return 50.00;
        }

        // Obtener primera carrera de preferencia del postulante
        $primeraCarrera = $inscripcion->postulacionCarreras()
            ->orderBy('prioridad')
            ->first();

        if (!$primeraCarrera) {
            return 50.00;
        }

        $cupo = CupoCarrera::where('carrera_id', $primeraCarrera->carrera_id)
            ->where('periodo_id', $periodoActivo->id)
            ->first();

        return $cupo?->monto_inscripcion ?? 50.00;
    }
}