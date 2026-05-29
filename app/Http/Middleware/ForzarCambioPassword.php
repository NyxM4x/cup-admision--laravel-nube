<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForzarCambioPassword
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->debe_cambiar_password) {
            // Rutas permitidas mientras tiene la flag activa:
            $rutasPermitidas = [
                'password.cambio.form',
                'password.cambio.store',
                'logout',
            ];

            if (! in_array($request->route()?->getName(), $rutasPermitidas)) {
                return redirect()->route('password.cambio.form')
                    ->with('warning', 'Debés cambiar tu contraseña antes de continuar.');
            }
        }

        return $next($request);
    }
}
