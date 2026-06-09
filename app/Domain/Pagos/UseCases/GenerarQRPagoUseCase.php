<?php

namespace App\Domain\Pagos\UseCases;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Models\Inscripcion;
use App\Models\Pago;

class GenerarQRPagoUseCase
{
    public function ejecutar(Inscripcion $inscripcion): Pago
    {
        // Si ya tiene pago aprobado, retornarlo
        $pagoExistente = $inscripcion->pago;

        if ($pagoExistente && $pagoExistente->estado === 'aprobado') {
            return $pagoExistente;
        }

        // Si tiene pago rechazado o no tiene, crear uno nuevo
        if (!$pagoExistente || $pagoExistente->estado === 'rechazado') {
            $pago = Pago::create([
                'inscripcion_id' => $inscripcion->id,
                'monto'          => 50.00,
                'metodo'         => 'QR',
                'referencia_qr'  => Pago::generarReferenciaQR($inscripcion->id),
                'estado'         => 'pendiente',
            ]);

            $inscripcion->update(['estado' => 'pago_pendiente']);

            BitacoraLogger::registrar(
                'PAGO_QR_GENERADO',
                'Pagos',
                "QR generado para inscripción #{$inscripcion->id} — Ref: {$pago->referencia_qr}"
            );

            return $pago;
        }

        return $pagoExistente;
    }
}