<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CambioPasswordObligatorioController extends Controller
{
    public function show()
    {
        return view('auth.cambio-password-obligatorio');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers(),
            ],
        ], [
            'current_password.required' => 'Debés ingresar tu contraseña actual.',
            'password.required' => 'Debés ingresar la nueva contraseña.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.mixed_case' => 'La contraseña debe tener mayúsculas y minúsculas.',
            'password.numbers' => 'La contraseña debe tener al menos un número.',
        ]);

        $user = Auth::user();

        // Verificar password actual
        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        // Verificar que la nueva sea distinta a la actual
        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'La nueva contraseña debe ser distinta a la actual.']);
        }

        // Actualizar
        $user->password = Hash::make($request->password);
        $user->debe_cambiar_password = false;
        $user->save();

        // Bitácora
        BitacoraLogger::registrar(
            'PASSWORD_CAMBIO_PRIMER_LOGIN',
            'Seguridad',
            'Usuario cambió su contraseña en primer login: '.$user->email,
            $user->id
        );

        // Redirigir según rol
        $rol = $user->rol->nombre ?? null;
        $redirect = match ($rol) {
            'Administrador' => '/dashboard/admin',
            'Coordinador CUP' => '/dashboard/coordinador',
            'Docente' => '/dashboard/docente',
            'Postulante' => '/dashboard/postulante',
            'Auditor' => '/dashboard/auditor',
            default => '/dashboard',
        };

        return redirect($redirect)->with('success', 'Contraseña actualizada correctamente. ¡Bienvenido al sistema!');
    }
}
