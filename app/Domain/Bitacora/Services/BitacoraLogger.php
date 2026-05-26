<?php

namespace App\Domain\Bitacora\Services;

use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Servicio compartido de bitácora. Lo usan todos los casos de uso para
 * registrar eventos auditables. NUNCA debe romper el flujo del caso de uso:
 * cualquier error al registrar se loguea y se ignora.
 */
class BitacoraLogger
{
    public static function registrar(
        string $accion,
        string $modulo,
        string $descripcion,
        ?int $userId = null,
        ?string $ip = null,
        ?string $userAgent = null
    ): void {
        try {
            // Si no se pasó userId, usar el autenticado (si lo hay)
            if ($userId === null && Auth::check()) {
                $userId = Auth::id();
            }

            // Datos de la request actual como fallback
            if ($ip === null) {
                $ip = request()->ip();
            }

            if ($userAgent === null) {
                $userAgent = request()->userAgent();
            }

            if ($userAgent !== null) {
                $userAgent = substr($userAgent, 0, 255);
            }

            $registro = new Bitacora([
                'user_id'     => $userId,
                'accion'      => $accion,
                'modulo'      => $modulo,
                'descripcion' => $descripcion,
                'ip'          => $ip,
                'user_agent'  => $userAgent,
            ]);
            $registro->created_at = now();
            $registro->save();
        } catch (\Throwable $e) {
            // La bitácora jamás debe interrumpir el caso de uso
            Log::error('BitacoraLogger: no se pudo registrar el evento', [
                'accion' => $accion,
                'modulo' => $modulo,
                'error'  => $e->getMessage(),
            ]);
        }
    }
}
