<?php

namespace App\Http\Controllers;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Models\Carrera;
use App\Models\Periodo;
use App\Models\ResultadoAdmision;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListaAdmitidosController extends Controller
{
    private const ADMITIDOS = ['admitido_primera', 'admitido_segunda'];
    private const RECHAZADOS = ['reprobado', 'no_admitido_sin_cupo'];

    public function index(Request $request)
    {
        $periodos = Periodo::orderBy('id', 'desc')->get();
        $periodoActivo = Periodo::where('activo', true)->orderBy('id', 'desc')->first();
        $periodoId = $request->input('periodo_id', $periodoActivo?->id);
        $periodo = $periodos->firstWhere('id', (int) $periodoId) ?? $periodoActivo;

        $q = trim($request->input('q', ''));
        $estado = $request->input('estado', 'todos'); // todos|admitidos|rechazados
        $carreraId = $request->input('carrera_id', 'todos');

        $carreras = Carrera::orderBy('nombre')->get();
        $resultados = collect();

        if ($periodo) {
            $query = ResultadoAdmision::with(['postulante.persona', 'carreraAsignada'])
                ->where('periodo_id', $periodo->id)
                ->orderByDesc('promedio_final');

            if ($estado === 'admitidos') {
                $query->whereIn('estado_admision', self::ADMITIDOS);
            } elseif ($estado === 'rechazados') {
                $query->whereIn('estado_admision', self::RECHAZADOS);
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

        return view('admision.lista-admitidos', compact(
            'periodo', 'periodos', 'periodoId', 'resultados', 'carreras', 'q', 'estado', 'carreraId'
        ));
    }

    public function publicar(Request $request)
    {
        $periodo = Periodo::where('activo', true)->orderBy('id', 'desc')->first();
        if (! $periodo) {
            return back()->withErrors(['general' => 'No hay un periodo activo.']);
        }

        $periodo->update(['lista_publicada' => true, 'fecha_publicacion' => now()]);

        BitacoraLogger::registrar(
            'LISTA_PUBLICADA',
            'Admision',
            "Lista oficial de admitidos publicada (periodo #{$periodo->id})",
            Auth::id()
        );

        return back()->with('success', 'Lista de admitidos publicada oficialmente.');
    }

    public function exportar(Request $request)
    {
        $periodo = Periodo::where('activo', true)->orderBy('id', 'desc')->first();
        if (! $periodo) {
            return back()->withErrors(['general' => 'No hay un periodo activo.']);
        }

        $admitidos = ResultadoAdmision::with(['postulante.persona', 'carreraAsignada'])
            ->where('periodo_id', $periodo->id)
            ->whereIn('estado_admision', self::ADMITIDOS)
            ->orderBy('carrera_asignada_id')
            ->orderByDesc('promedio_final')
            ->get();

        $pdf = Pdf::loadView('admision.pdf.lista-admitidos', compact('periodo', 'admitidos'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("lista_admitidos_periodo_{$periodo->id}.pdf");
    }
}
