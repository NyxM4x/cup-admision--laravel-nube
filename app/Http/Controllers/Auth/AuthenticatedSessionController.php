<?php

namespace App\Http\Controllers\Auth;

use App\Domain\Seguridad\UseCases\AutenticarUsuarioUseCase;
use App\Domain\Seguridad\UseCases\CerrarSesionUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     * Solo delega al caso de uso (CU01).
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $useCase = new AutenticarUsuarioUseCase();
        $resultado = $useCase->ejecutar(
            $request->only('email', 'password'),
            $request->boolean('remember')
        );

        if (! $resultado['exito']) {
            return back()->withErrors(['email' => $resultado['mensaje']])->onlyInput('email');
        }

        return redirect()->intended($resultado['redirect']);
    }

    /**
     * Destroy an authenticated session.
     * Solo delega al caso de uso (CU02).
     */
    public function destroy(Request $request): RedirectResponse
    {
        $useCase = new CerrarSesionUseCase();
        $useCase->ejecutar();

        return redirect('/');
    }
}
