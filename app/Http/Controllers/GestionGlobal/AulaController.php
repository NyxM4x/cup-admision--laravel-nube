<?php

namespace App\Http\Controllers\GestionGlobal;

use App\Domain\GestionGlobal\Aulas\Repositories\AulaRepository;
use App\Domain\GestionGlobal\Aulas\UseCases\CrearAulaUseCase;
use App\Domain\GestionGlobal\Aulas\UseCases\EditarAulaUseCase;
use App\Domain\GestionGlobal\Aulas\UseCases\InactivarAulaUseCase;
use App\Domain\GestionGlobal\Aulas\UseCases\ListarAulasUseCase;
use App\Domain\GestionGlobal\Aulas\UseCases\ReactivarAulaUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\GestionGlobal\StoreAulaRequest;
use App\Http\Requests\GestionGlobal\UpdateAulaRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AulaController extends Controller
{
    public function __construct(
        private ListarAulasUseCase $listarUseCase,
        private CrearAulaUseCase $crearUseCase,
        private EditarAulaUseCase $editarUseCase,
        private InactivarAulaUseCase $inactivarUseCase,
        private ReactivarAulaUseCase $reactivarUseCase,
        private AulaRepository $repo,
    ) {}

    public function index(Request $request): View
    {
        $q = $request->input('q');
        $estado = $request->input('estado', 'activos');
        $edificio = $request->input('edificio');

        $aulas = $this->listarUseCase->ejecutar($q, $estado, $edificio);
        $edificios = $this->repo->edificiosDisponibles();
        $estadisticas = $this->listarUseCase->obtenerEstadisticas();

        return view('gestion-global.aulas.index', compact('aulas', 'edificios', 'estadisticas', 'q', 'estado', 'edificio'));
    }

    public function create(): View
    {
        return view('gestion-global.aulas.create');
    }

    public function store(StoreAulaRequest $request): RedirectResponse
    {
        try {
            $this->crearUseCase->ejecutar($request->validated());

            return redirect()->route('aulas.index')->with('success', 'Aula creada correctamente.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function edit(int $aula): View
    {
        $aulaModel = $this->repo->obtenerPorId($aula);
        abort_if(! $aulaModel, 404);

        return view('gestion-global.aulas.edit', compact('aulaModel'));
    }

    public function update(UpdateAulaRequest $request, int $aula): RedirectResponse
    {
        try {
            $this->editarUseCase->ejecutar($aula, $request->validated());

            return redirect()->route('aulas.index')->with('success', 'Aula actualizada.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy(int $aula): RedirectResponse
    {
        try {
            $this->inactivarUseCase->ejecutar($aula);

            return back()->with('success', 'Aula inactivada.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }

    public function reactivar(int $aula): RedirectResponse
    {
        $this->reactivarUseCase->ejecutar($aula);

        return back()->with('success', 'Aula reactivada.');
    }
}
