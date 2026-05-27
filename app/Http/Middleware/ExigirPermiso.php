<?php

namespace App\Http\Middleware;

use App\Domain\Bitacora\Services\BitacoraLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ExigirPermiso
{
    /**
     * Verifica que el usuario autenticado tenga el permiso indicado.
     * El rol Administrador siempre pasa.
     */
    public function handle(Request $request, Closure $next, string $codigoPermiso): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect('/login');
        }

        // Administrador siempre pasa
        if ($user->rol && $user->rol->nombre === 'Administrador') {
            return $next($request);
        }

        // Verificar permiso concreto
        if (! $user->tienePermiso($codigoPermiso)) {
            BitacoraLogger::registrar(
                'ACCESO_DENEGADO',
                'Seguridad',
                'Intento de acceso sin permiso: '.$codigoPermiso.' en '.$request->fullUrl()
            );

            abort(403, 'No tenés permiso para realizar esta acción.');
        }

        return $next($request);
    }
}
