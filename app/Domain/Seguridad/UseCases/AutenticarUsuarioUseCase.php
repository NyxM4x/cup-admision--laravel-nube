<?php

namespace App\Domain\Seguridad\UseCases;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * CU01 - Iniciar sesión.
 *
 * Envuelve la autenticación de Breeze añadiendo: validación de usuario activo,
 * registro en bitácora y redirección según rol.
 */
class AutenticarUsuarioUseCase
{
    /** Ruta de dashboard según el nombre del rol. */
    private const REDIRECTS_POR_ROL = [
        'Administrador'   => '/dashboard/admin',
        'Coordinador CUP' => '/dashboard/coordinador',
        'Docente'         => '/dashboard/docente',
        'Postulante'      => '/dashboard/postulante',
        'Auditor'         => '/dashboard/auditor',
    ];

    /**
     * @param  array{email: string, password: string}  $credenciales
     * @return array{exito: bool, mensaje: string, usuario: ?User, redirect: string}
     */
    public function ejecutar(array $credenciales, bool $remember = false): array
    {
        // 1. Intento de autenticación
        if (! Auth::attempt($credenciales, $remember)) {
            BitacoraLogger::registrar(
                'LOGIN_FAIL',
                'Seguridad',
                'Intento fallido con email='.($credenciales['email'] ?? ''),
            );

            return $this->resultado(false, 'Credenciales incorrectas');
        }

        /** @var User $user */
        $user = Auth::user();

        // 2. Usuario inactivo -> cerrar sesión de inmediato
        if (! $user->activo) {
            Auth::logout();

            BitacoraLogger::registrar(
                'LOGIN_INACTIVO',
                'Seguridad',
                'Intento de login de usuario inactivo: '.$user->email,
                $user->id,
            );

            return $this->resultado(false, 'Tu cuenta está inactiva. Contactá al administrador.');
        }

        // 3. Login correcto: regenerar sesión (anti session fixation) y registrar
        request()->session()->regenerate();

        BitacoraLogger::registrar(
            'LOGIN_OK',
            'Seguridad',
            'Sesión iniciada: '.$user->email,
            $user->id,
        );

        return $this->resultado(true, 'Bienvenido', $user, $this->resolverRedirect($user));
    }

    private function resolverRedirect(User $user): string
    {
        $rol = $user->rol?->nombre;

        return self::REDIRECTS_POR_ROL[$rol] ?? '/dashboard';
    }

    /**
     * @return array{exito: bool, mensaje: string, usuario: ?User, redirect: string}
     */
    private function resultado(bool $exito, string $mensaje, ?User $usuario = null, string $redirect = ''): array
    {
        return [
            'exito'    => $exito,
            'mensaje'  => $mensaje,
            'usuario'  => $usuario,
            'redirect' => $redirect,
        ];
    }
}
