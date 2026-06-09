<?php

namespace App\Domain\Pagos\UseCases;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Models\Inscripcion;
use App\Models\Pago;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcesarPayPalUseCase
{
    private function getAccessToken(): ?string
    {
        try {
            $response = Http::withBasicAuth(
                config('pagos.paypal.client_id'),
                config('pagos.paypal.client_secret')
            )->asForm()->post(
                config('pagos.paypal.base_url') . '/v1/oauth2/token',
                ['grant_type' => 'client_credentials']
            );

            if ($response->failed()) {
                Log::error('PayPal Token Error:', ['response' => $response->json()]);
                return null;
            }

            return $response->json('access_token');
        } catch (\Exception $e) {
            Log::error('PayPal Token Exception:', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function crearOrden(Inscripcion $inscripcion): array
    {
        try {
            $token = $this->getAccessToken();
            
            if (!$token) {
                Log::error('PayPal: No se pudo obtener token');
                return ['order_id' => null, 'approve_url' => null, 'error' => 'No se pudo conectar con PayPal'];
            }

            $response = Http::withToken($token)
                ->post(config('pagos.paypal.base_url') . '/v2/checkout/orders', [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [[
                        'amount' => [
                            'currency_code' => 'USD',
                            'value'         => '5.00',
                        ],
                        'description' => 'Inscripcion CUP FICCT UAGRM',
                    ]],
                    'application_context' => [
                        'return_url' => route('pagos.paypal.retorno'),
                        'cancel_url' => route('pagos.paypal.cancelar-general'),
                        'brand_name' => 'CUP FICCT UAGRM',
                        'user_action' => 'PAY_NOW',
                        'shipping_preference' => 'NO_SHIPPING',  // 👈 AGREGAR ESTO
                    ],
                ]);

            if ($response->failed()) {
                Log::error('PayPal Create Order Error:', ['response' => $response->json()]);
                return ['order_id' => null, 'approve_url' => null, 'error' => 'Error al crear orden PayPal'];
            }

            $order = $response->json();
            Log::info('PayPal Order Created:', ['order_id' => $order['id'] ?? 'unknown']);

            Pago::updateOrCreate(
                ['inscripcion_id' => $inscripcion->id, 'estado' => 'pendiente'],
                [
                    'monto'         => 5.00,
                    'metodo'        => 'PayPal',
                    'referencia_qr' => $order['id'] ?? null,
                    'estado'        => 'pendiente',
                ]
            );

            $inscripcion->update(['estado' => 'pago_pendiente']);

            $approveUrl = collect($order['links'] ?? [])
                ->firstWhere('rel', 'approve')['href'] ?? null;

            BitacoraLogger::registrar(
                'PAYPAL_ORDEN_CREADA',
                'Pagos',
                "Orden PayPal creada: " . ($order['id'] ?? 'desconocida') . " — Inscripcion #{$inscripcion->id}"
            );

            return [
                'order_id'    => $order['id'] ?? null,
                'approve_url' => $approveUrl,
            ];
            
        } catch (\Exception $e) {
            Log::error('PayPal Exception:', ['message' => $e->getMessage(), 'line' => $e->getLine()]);
            return ['order_id' => null, 'approve_url' => null, 'error' => $e->getMessage()];
        }
    }

    public function capturarOrden(string $orderId, Inscripcion $inscripcion): bool
    {
        try {
            $token = $this->getAccessToken();
            
            if (!$token) {
                Log::error('PayPal: No se pudo obtener token para capturar');
                return false;
            }

            $response = Http::withToken($token)
                ->post(config('pagos.paypal.base_url') . "/v2/checkout/orders/{$orderId}/capture");

            $result = $response->json();

            if (($result['status'] ?? '') === 'COMPLETED') {
                $pago = Pago::where('referencia_qr', $orderId)->first();
                if ($pago) {
                    $pago->update(['estado' => 'aprobado']);
                    $inscripcion->update(['estado' => 'pagado']);
                }

                BitacoraLogger::registrar(
                    'PAYPAL_PAGO_CAPTURADO',
                    'Pagos',
                    "Pago PayPal capturado: {$orderId}"
                );

                return true;
            }

            Log::error('PayPal Capture Error:', ['response' => $result]);
            return false;
            
        } catch (\Exception $e) {
            Log::error('PayPal Capture Exception:', ['message' => $e->getMessage()]);
            return false;
        }
    }
}