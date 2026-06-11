<?php

namespace App\Domain\Pagos\UseCases;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Models\Pago;

class AprobarPagoUseCase
{
    public function ejecutar(Pago $pago, int $adminId): array
    {
        // 1. Aprobar el pago
        $pago->update([
            'estado'       => 'aprobado',
            'revisado_por' => $adminId,
            'fecha_pago'   => now(),
        ]);

        // 2. Actualizar estado inscripción → habilitado
        $inscripcion = $pago->inscripcion;
        $inscripcion->update(['estado' => 'habilitado']);

        $persona = $inscripcion->postulante->persona;

        BitacoraLogger::registrar(
            'PAGO_APROBADO',
            'Pagos',
            "Pago #{$pago->id} aprobado — Postulante CI:{$persona->ci}",
            $adminId
        );

        return [
            'correo' => $persona->correo,
        ];
    }
}