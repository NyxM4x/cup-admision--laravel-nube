<?php

namespace App\Http\Controllers\Seguridad;

use App\Domain\Usuarios\Repositories\UsuarioRepository;
use App\Domain\Usuarios\UseCases\CrearUsuarioUseCase;
use App\Domain\Usuarios\UseCases\EditarUsuarioUseCase;
use App\Domain\Usuarios\UseCases\InactivarUsuarioUseCase;
use App\Domain\Usuarios\UseCases\ListarUsuariosUseCase;
use App\Domain\Usuarios\UseCases\ReactivarUsuarioUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Seguridad\StoreUsuarioRequest;
use App\Http\Requests\Seguridad\UpdateUsuarioRequest;
use App\Models\Rol;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function __construct(
        private ListarUsuariosUseCase $listarUseCase,
        private CrearUsuarioUseCase $crearUseCase,
        private EditarUsuarioUseCase $editarUseCase,
        private InactivarUsuarioUseCase $inactivarUseCase,
        private ReactivarUsuarioUseCase $reactivarUseCase,
        private UsuarioRepository $repo,
    ) {}

    public function index(Request $request): View
    {
        $q = $request->input('q');
        $estado = $request->input('estado', 'activos'); // activos | inactivos | todos

        $usuarios = $this->listarUseCase->ejecutar($q, $estado);
        $roles = Rol::activos()->orderBy('nombre')->get();

        return view('seguridad.usuarios.index', compact('usuarios', 'roles', 'q', 'estado'));
    }

    public function create(): View
    {
        $roles = Rol::activos()->orderBy('nombre')->get();

        return view('seguridad.usuarios.create', compact('roles'));
    }

    public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        try {
            $this->crearUseCase->ejecutar($request->validated());

            return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function edit(int $usuario): View
    {
        $user = $this->repo->obtenerPorId($usuario);
        abort_if(! $user, 404);

        $roles = Rol::activos()->orderBy('nombre')->get();

        return view('seguridad.usuarios.edit', compact('user', 'roles'));
    }

    public function update(UpdateUsuarioRequest $request, int $usuario): RedirectResponse
    {
        try {
            $this->editarUseCase->ejecutar($usuario, $request->validated());

            return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(int $usuario): RedirectResponse
    {
        // Soft-delete = inactivar
        try {
            $this->inactivarUseCase->ejecutar($usuario);

            return back()->with('success', 'Usuario inactivado.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }

    public function reactivar(int $usuario): RedirectResponse
    {
        $this->reactivarUseCase->ejecutar($usuario);

        return back()->with('success', 'Usuario reactivado.');
    }
}
