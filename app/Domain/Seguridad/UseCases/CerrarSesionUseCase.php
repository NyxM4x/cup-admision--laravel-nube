<?php

namespace App\Domain\Seguridad\UseCases;

use App\Domain\Bitacora\Services\BitacoraLogger;
use Illuminate\Support\Facades\Auth;

/**
 * CU02 - Cerrar sesión.
 */
class CerrarSesionUseCase
{
    public function ejecutar(): void
    {
        // Capturar datos del usuario ANTES de cerrar la sesión
        $user   = Auth::user();
        $userId = $user?->id;
        $email  = $user?->email ?? 'desconocido';

        Auth::guard('web')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        // userId explícito porque ya no hay Auth::user()
        BitacoraLogger::registrar(
            'LOGOUT_OK',
            'Seguridad',
            'Cierre de sesión: '.$email,
            $userId,
        );
    }
}
