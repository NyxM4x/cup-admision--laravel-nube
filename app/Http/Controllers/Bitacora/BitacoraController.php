<?php

namespace App\Http\Controllers\Bitacora;

use App\Domain\Bitacora\UseCases\ConsultarBitacoraUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BitacoraController extends Controller
{
    public function __construct(private ConsultarBitacoraUseCase $useCase) {}

    public function index(Request $request): View
    {
        $filtros = $request->only([
            'user_id', 'modulo', 'accion', 'ip', 'q', 'fecha_desde', 'fecha_hasta',
        ]);

        $registros = $this->useCase->listar($filtros, 25);
        $opciones = $this->useCase->obtenerOpcionesFiltros();
        $estadisticas = $this->useCase->obtenerEstadisticas();

        return view('bitacora.index', compact('registros', 'opciones', 'estadisticas', 'filtros'));
    }

    public function show(int $bitacora): View
    {
        $registro = $this->useCase->obtenerDetalle($bitacora);
        abort_if(! $registro, 404);

        return view('bitacora.show', compact('registro'));
    }
}
