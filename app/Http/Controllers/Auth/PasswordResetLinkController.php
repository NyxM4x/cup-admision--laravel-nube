<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Http\Controllers\Controller;
use App\Mail\RecuperarPasswordMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            // Por seguridad NO revelamos si el email existe o no
            return back()->with('status', 'Si el correo está registrado, te enviaremos un enlace de recuperación.');
        }

        // Generar token usando el sistema de Laravel
        $token = Password::createToken($user);

        // URL absoluta para el email
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ], false));

        // Enviar mail con nuestra plantilla custom
        try {
            Mail::to($user->email)->send(new RecuperarPasswordMail(
                resetUrl: $resetUrl,
                nombreUsuario: $user->name,
                emailUsuario: $user->email,
                minutos: 60,
            ));

            BitacoraLogger::registrar(
                'PASSWORD_RESET_SOLICITADO',
                'Seguridad',
                'Solicitud de reseteo de password para: '.$user->email,
                $user->id
            );
        } catch (\Exception $e) {
            Log::error('Error enviando mail de reset: '.$e->getMessage());

            return back()->withErrors(['email' => 'No pudimos enviar el correo. Contactá al administrador.']);
        }

        return back()->with('status', 'Te enviamos un correo con instrucciones para recuperar tu contraseña. Revisá tu bandeja de entrada.');
    }
}
