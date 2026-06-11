<?php

namespace App\Domain\Pagos\UseCases;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Models\Inscripcion;
use App\Models\Pago;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class ProcesarStripeUseCase
{
    public function __construct(
        private ObtenerMontoInscripcionUseCase $montoUseCase
    ) {}

    public function crearIntento(Inscripcion $inscripcion): array
    {
        Stripe::setApiKey(config('pagos.stripe.secret'));

        // Obtener monto dinámico de la carrera
        $monto = $this->montoUseCase->ejecutar($inscripcion);
        $montoCentavos = (int) ($monto * 100);

        $intent = PaymentIntent::create([
            'amount'   => $montoCentavos,
            'currency' => 'usd',
            'metadata' => [
                'inscripcion_id' => $inscripcion->id,
                'postulante_ci'  => $inscripcion->postulante->persona->ci,
            ],
        ]);

        $pago = Pago::updateOrCreate(
            ['inscripcion_id' => $inscripcion->id, 'estado' => 'pendiente'],
            [
                'monto'         => $monto,
                'metodo'        => 'Stripe',
                'referencia_qr' => $intent->id,
                'estado'        => 'pendiente',
            ]
        );

        $inscripcion->update(['estado' => 'pago_pendiente']);

        BitacoraLogger::registrar(
            'STRIPE_INTENTO_CREADO',
            'Pagos',
            "PaymentIntent creado: {$intent->id} — Inscripcion #{$inscripcion->id}"
        );

        return [
            'client_secret'  => $intent->client_secret,
            'payment_intent' => $intent->id,
            'monto'          => $monto,
            'pago'           => $pago,
        ];
    }

    public function confirmar(string $paymentIntentId, Inscripcion $inscripcion): bool
    {
        Stripe::setApiKey(config('pagos.stripe.secret'));

        $intent = PaymentIntent::retrieve($paymentIntentId);

        if ($intent->status === 'succeeded') {
            $pago = Pago::where('referencia_qr', $paymentIntentId)->first();
            if ($pago) {
                $pago->update(['estado' => 'pendiente', 'metodo' => 'Stripe']);
            }

            BitacoraLogger::registrar(
                'STRIPE_PAGO_RECIBIDO',
                'Pagos',
                "Pago Stripe confirmado: {$paymentIntentId}"
            );

            return true;
        }

        return false;
    }
}