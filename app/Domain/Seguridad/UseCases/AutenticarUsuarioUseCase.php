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
        $email = $credenciales['email'] ?? '';
        $user = User::where('email', $email)->first();

        if ($user && $user->bloqueado_hasta && now()->lessThan($user->bloqueado_hasta)) {
            BitacoraLogger::registrar(
                'LOGIN_BLOQUEADO',
                'Seguridad',
                'Intento de login bloqueado por exceso de intentos: '.$email,
                $user->id,
            );

            return $this->resultado(
                false,
                'Tu cuenta está bloqueada por exceso de intentos. Intenta de nuevo en 15 minutos o contacta a un administrador.'
            );
        }

        if (! Auth::attempt($credenciales, $remember)) {
            if ($user) {
                $this->registrarIntentoFallido($user);
            } else {
                BitacoraLogger::registrar(
                    'LOGIN_FAIL',
                    'Seguridad',
                    'Intento fallido con email='.$email,
                );
            }

            return $this->resultado(false, 'Credenciales incorrectas');
        }

        /** @var User $user */
        $user = Auth::user();

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

        $this->restablecerIntentosDeLogin($user);

        request()->session()->regenerate();

        BitacoraLogger::registrar(
            'LOGIN_OK',
            'Seguridad',
            'Sesión iniciada: '.$user->email,
            $user->id,
        );

        return $this->resultado(true, 'Bienvenido', $user, $this->resolverRedirect($user));
    }

    private function registrarIntentoFallido(User $user): void
    {
        $user->failed_logins = ($user->failed_logins ?? 0) + 1;

        if ($user->failed_logins >= 3) {
            $user->failed_logins = 0;
            $user->bloqueado_hasta = now()->addMinutes(15);
            $user->save();

            BitacoraLogger::registrar(
                'LOGIN_LOCKED',
                'Seguridad',
                'Usuario bloqueado por 3 intentos fallidos: '.$user->email,
                $user->id,
            );

            return;
        }

        $user->save();
        BitacoraLogger::registrar(
            'LOGIN_FAIL',
            'Seguridad',
            'Intento fallido con email='.$user->email,
            $user->id,
        );
    }

    private function restablecerIntentosDeLogin(User $user): void
    {
        if ($user->failed_logins > 0 || $user->bloqueado_hasta) {
            $user->failed_logins = 0;
            $user->bloqueado_hasta = null;
            $user->save();
        }
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
