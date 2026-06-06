<?php

namespace App\Http\Controllers;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Models\Carrera;
use App\Models\Periodo;
use App\Models\ResultadoAdmision;
use App\Services\AsignacionCarreraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AsignacionCarreraController extends Controller
{
    // CU24 — Ranking ANTES de asignar (vista previa)
    public function vistaPreasignacion()
    {
        $periodo = Periodo::where('activo', true)->orderBy('id', 'desc')->first();

        $ranking = collect();
        $resumen = ['total' => 0, 'aprobados' => 0, 'reprobados' => 0, 'asignados' => 0];

        if ($periodo) {
            $ranking = ResultadoAdmision::with('postulante.persona')
                ->where('periodo_id', $periodo->id)
                ->where('estado_admision', 'aprobado')
                ->orderByDesc('promedio_final')
                ->paginate(50);

            $base = ResultadoAdmision::where('periodo_id', $periodo->id);
            $resumen = [
                'total'      => (clone $base)->count(),
                'aprobados'  => (clone $base)->where('estado_admision', 'aprobado')->count(),
                'reprobados' => (clone $base)->where('estado_admision', 'reprobado')->count(),
                'asignados'  => (clone $base)->whereIn('estado_admision', ['admitido_primera', 'admitido_segunda'])->count(),
            ];
        }

        return view('admision.preasignacion', compact('periodo', 'ranking', 'resumen'));
    }

    // CU24 — Ejecutar el algoritmo de asignación
    public function ejecutar(Request $request, AsignacionCarreraService $service)
    {
        $periodo = Periodo::where('activo', true)->orderBy('id', 'desc')->first();
        if (! $periodo) {
            return back()->withErrors(['general' => 'No hay un periodo activo.']);
        }

        $stats = $service->ejecutarAsignacion($periodo);

        BitacoraLogger::registrar(
            'ASIGNACION_EJECUTADA',
            'Admision',
            "Asignación de carreras ejecutada (periodo #{$periodo->id}): "
                ."{$stats['admitidos_primera']} 1ra, {$stats['admitidos_segunda']} 2da, {$stats['sin_cupo']} sin cupo",
            Auth::id()
        );

        return redirect()->route('admision.resultados')
            ->with('success', 'Asignación ejecutada correctamente.')
            ->with('stats_asignacion', $stats);
    }

    // CU24 — Resultados de la asignación (con filtros)
    public function vistaResultados(Request $request)
    {
        $periodo = Periodo::where('activo', true)->orderBy('id', 'desc')->first();

        $q = trim($request->input('q', ''));
        $estado = $request->input('estado', 'todos');
        $carreraId = $request->input('carrera_id', 'todos');

        $carreras = Carrera::orderBy('nombre')->get();
        $resultados = collect();

        if ($periodo) {
            $query = ResultadoAdmision::with(['postulante.persona', 'carreraAsignada'])
                ->where('periodo_id', $periodo->id)
                ->orderByDesc('promedio_final');

            if ($estado !== 'todos') {
                $query->where('estado_admision', $estado);
            }
            if ($carreraId !== 'todos') {
                $query->where('carrera_asignada_id', $carreraId);
            }
            if ($q !== '') {
                $query->whereHas('postulante.persona', function ($w) use ($q) {
                    $w->whereRaw('unaccent(nombre) ilike unaccent(?)', ["%{$q}%"])
                      ->orWhere('ci', 'ilike', "%{$q}%");
                });
            }

            $resultados = $query->paginate(30)->withQueryString();
        }

        return view('admision.resultados', compact('periodo', 'resultados', 'carreras', 'q', 'estado', 'carreraId'));
    }
}
