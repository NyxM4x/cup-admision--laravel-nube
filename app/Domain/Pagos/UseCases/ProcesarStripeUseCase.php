<?php

namespace App\Domain\Pagos\UseCases;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Models\Inscripcion;
use App\Models\Pago;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class ProcesarStripeUseCase
{
    public function crearIntento(Inscripcion $inscripcion): array
    {
        if (!class_exists(\Stripe\Stripe::class)) {
            throw new \RuntimeException("Stripe SDK not installed. Run: composer require stripe/stripe-php");
        }

        Stripe::setApiKey(config('pagos.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount'   => 5000, // Bs. 50.00 en centavos
            'currency' => 'usd', // Stripe sandbox no soporta BOB
            'metadata' => [
                'inscripcion_id' => $inscripcion->id,
                'postulante_ci'  => $inscripcion->postulante->persona->ci,
            ],
        ]);

        // Registrar pago pendiente
        $pago = Pago::updateOrCreate(
            ['inscripcion_id' => $inscripcion->id, 'estado' => 'pendiente'],
            [
                'monto'         => 50.00,
                'metodo'        => 'Stripe',
                'referencia_qr' => $intent->id,
                'estado'        => 'pendiente',
            ]
        );

        $inscripcion->update(['estado' => 'pago_pendiente']);

        BitacoraLogger::registrar(
            'STRIPE_INTENTO_CREADO',
            'Pagos',
            "PaymentIntent creado: {$intent->id} — Inscripción #{$inscripcion->id}"
        );

        return [
            'client_secret'  => $intent->client_secret,
            'payment_intent' => $intent->id,
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
                $pago->update([
                    'estado'     => 'pendiente', // Admin confirma
                    'metodo'     => 'Stripe',
                ]);
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