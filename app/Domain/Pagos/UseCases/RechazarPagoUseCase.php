<?php

namespace App\Domain\Pagos\UseCases;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Models\Pago;

class RechazarPagoUseCase
{
    public function ejecutar(Pago $pago, int $adminId, string $observacion): void
    {
        $pago->update([
            'estado'       => 'rechazado',
            'observacion'  => $observacion,
            'revisado_por' => $adminId,
        ]);

        $pago->inscripcion->update(['estado' => 'pago_rechazado']);

        BitacoraLogger::registrar(
            'PAGO_RECHAZADO',
            'Pagos',
            "Pago #{$pago->id} rechazado — Motivo: {$observacion}",
            $adminId
        );
    }
}