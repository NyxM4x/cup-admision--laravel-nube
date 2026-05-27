<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Seguridad\UseCases\AutenticarUsuarioUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function __construct(
        private AutenticarUsuarioUseCase $autenticarUseCase
    ) {}

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $resultado = $this->autenticarUseCase->ejecutar(
            credenciales: $request->only('email', 'password'),
            remember: $request->boolean('remember'),
        );

        if (! $resultado['exito']) {
            return back()
                ->withErrors(['email' => $resultado['mensaje']])
                ->withInput($request->only('email'));
        }

        return redirect($resultado['redirect']);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}