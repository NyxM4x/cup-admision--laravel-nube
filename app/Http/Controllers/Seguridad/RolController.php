<?php

namespace App\Http\Controllers\Seguridad;

use App\Domain\Seguridad\Repositories\RolRepository;
use App\Domain\Seguridad\UseCases\CrearRolUseCase;
use App\Domain\Seguridad\UseCases\EditarRolUseCase;
use App\Domain\Seguridad\UseCases\InactivarRolUseCase;
use App\Domain\Seguridad\UseCases\ListarRolesUseCase;
use App\Domain\Seguridad\UseCases\ReactivarRolUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Seguridad\StoreRolRequest;
use App\Http\Requests\Seguridad\UpdateRolRequest;
use App\Models\Permiso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RolController extends Controller
{
    public function __construct(
        private ListarRolesUseCase $listarUseCase,
        private CrearRolUseCase $crearUseCase,
        private EditarRolUseCase $editarUseCase,
        private InactivarRolUseCase $inactivarUseCase,
        private ReactivarRolUseCase $reactivarUseCase,
        private RolRepository $repo,
    ) {}

    public function index(Request $request): View
    {
        $q = $request->input('q');
        $estado = $request->input('estado', 'activos');

        $roles = $this->listarUseCase->ejecutar($q, $estado);

        return view('seguridad.roles.index', compact('roles', 'q', 'estado'));
    }

    public function create(): View
    {
        $permisos = Permiso::orderBy('modulo')->orderBy('codigo')->get()->groupBy('modulo');

        return view('seguridad.roles.create', compact('permisos'));
    }

    public function store(StoreRolRequest $request): RedirectResponse
    {
        try {
            $data = $request->only(['nombre', 'descripcion']);
            $permisoIds = $request->input('permisos', []);
            $this->crearUseCase->ejecutar($data, $permisoIds);

            return redirect()->route('roles.index')->with('success', 'Rol creado correctamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function edit(int $rol): View
    {
        $rolModel = $this->repo->obtenerPorId($rol);
        abort_if(! $rolModel, 404);

        $permisos = Permiso::orderBy('modulo')->orderBy('codigo')->get()->groupBy('modulo');
        $permisosAsignados = $rolModel->permisos->pluck('id')->toArray();

        return view('seguridad.roles.edit', compact('rolModel', 'permisos', 'permisosAsignados'));
    }

    public function update(UpdateRolRequest $request, int $rol): RedirectResponse
    {
        try {
            $data = $request->only(['nombre', 'descripcion']);
            $permisoIds = $request->input('permisos', []);
            $this->editarUseCase->ejecutar($rol, $data, $permisoIds);

            return redirect()->route('roles.index')->with('success', 'Rol actualizado.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(int $rol): RedirectResponse
    {
        try {
            $this->inactivarUseCase->ejecutar($rol);

            return back()->with('success', 'Rol inactivado.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }

    public function reactivar(int $rol): RedirectResponse
    {
        $this->reactivarUseCase->ejecutar($rol);

        return back()->with('success', 'Rol reactivado.');
    }
}
