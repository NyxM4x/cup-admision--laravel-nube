<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\ResultadoAdmision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstadisticaController extends Controller
{
    private function periodo(Request $request): ?Periodo
    {
        $activo = Periodo::where('activo', true)->orderBy('id', 'desc')->first();
        $id = $request->input('periodo_id', $activo?->id);

        return Periodo::find($id) ?? $activo;
    }

    // CU27 — Dashboard con gráficos (Chart.js)
    public function dashboard(Request $request)
    {
        $periodo  = $this->periodo($request);
        $periodos = Periodo::orderBy('id', 'desc')->get();

        $porCarrera    = collect();
        $aprobReprob   = ['aprobados' => 0, 'reprobados' => 0];
        $promedioMateria = collect();

        if ($periodo) {
            // Distribución por 1ra preferencia de carrera
            $porCarrera = DB::table('resultados_admision as r')
                ->join('inscripciones as i', function ($j) {
                    $j->on('i.postulante_id', '=', 'r.postulante_id')
                      ->on('i.periodo_id', '=', 'r.periodo_id');
                })
                ->join('postulacion_carreras as pc', function ($j) {
                    $j->on('pc.inscripcion_id', '=', 'i.id')->where('pc.prioridad', '=', 1);
                })
                ->join('carreras as c', 'c.id', '=', 'pc.carrera_id')
                ->where('r.periodo_id', $periodo->id)
                ->groupBy('c.nombre')
                ->selectRaw('c.nombre, count(*) as total')
                ->pluck('total', 'nombre');

            $aprobReprob = [
                'aprobados'  => ResultadoAdmision::where('periodo_id', $periodo->id)->where('promedio_final', '>=', 51)->count(),
                'reprobados' => ResultadoAdmision::where('periodo_id', $periodo->id)->where('promedio_final', '<', 51)->count(),
            ];

            // Promedio real por materia desde la tabla notas
            $promedioMateria = DB::table('notas as n')
                ->join('grupo_materias as gm', 'gm.id', '=', 'n.grupo_materia_id')
                ->join('grupos as g', 'g.id', '=', 'gm.grupo_id')
                ->join('materias as m', 'm.id', '=', 'gm.materia_id')
                ->where('g.periodo_id', $periodo->id)
                ->whereNotNull('n.nota_final')
                ->groupBy('m.nombre', 'm.sigla')
                ->selectRaw('m.nombre, m.sigla, ROUND(AVG(n.nota_final)::numeric, 2) as promedio')
                ->orderBy('m.sigla')
                ->get()
                ->mapWithKeys(fn ($r) => [$r->nombre => (float) $r->promedio]);

            // Si aún no hay notas, mostrar promedio global referencial
            if ($promedioMateria->isEmpty()) {
                $globalAvg = (float) ResultadoAdmision::where('periodo_id', $periodo->id)->avg('promedio_final');
                $promedioMateria = Materia::where('activo', true)->orderBy('sigla')->get()
                    ->mapWithKeys(fn ($m) => [$m->nombre => round($globalAvg, 2)]);
            }
        }

        return view('estadisticas.dashboard', compact('periodo', 'periodos', 'porCarrera', 'aprobReprob', 'promedioMateria'));
    }

    // CU27 — Estadísticas por docente (satisfacción = % aprobados en sus grupos)
    public function porDocente(Request $request)
    {
        $periodo  = $this->periodo($request);
        $periodos = Periodo::orderBy('id', 'desc')->get();

        // Estadísticas reales por docente desde tabla notas
        $statsDocente = collect();
        if ($periodo) {
            $statsDocente = DB::table('notas as n')
                ->join('grupo_materias as gm', 'gm.id', '=', 'n.grupo_materia_id')
                ->join('grupos as g', 'g.id', '=', 'gm.grupo_id')
                ->where('g.periodo_id', $periodo->id)
                ->whereNotNull('gm.docente_id')
                ->whereNotNull('n.nota_final')
                ->groupBy('gm.docente_id')
                ->selectRaw('
                    gm.docente_id,
                    COUNT(*) as total_notas,
                    ROUND(AVG(n.nota_final)::numeric, 2) as promedio,
                    SUM(CASE WHEN n.nota_final >= 51 THEN 1 ELSE 0 END) as aprobados
                ')
                ->get()
                ->keyBy('docente_id');
        }

        $docentes = Docente::with('persona', 'profesion')
            ->where('activo', true)
            ->get()
            ->map(function ($d) use ($periodo, $statsDocente) {
                $grupos = 0;

                if ($periodo) {
                    $grupos = Grupo::where('periodo_id', $periodo->id)
                        ->whereHas('grupoMaterias', fn ($q) => $q->where('docente_id', $d->id))
                        ->count();
                }

                $stats = $statsDocente->get($d->id);
                $promedio = null;
                $pctAprobados = null;
                $satisfaccion = null;

                if ($stats && $stats->total_notas > 0) {
                    $promedio = (float) $stats->promedio;
                    $pctAprobados = round($stats->aprobados * 100 / $stats->total_notas, 1);
                    // Satisfacción = porcentaje de aprobados (100% = excelente, 0% = pésimo)
                    $satisfaccion = $pctAprobados;
                }

                return (object) [
                    'id'            => $d->id,
                    'nombre'        => $d->persona->nombre ?? ('Docente #' . $d->id),
                    'materia'       => $d->materia ?? 'Sin asignar',
                    'profesion'     => $d->profesion->nombre ?? '—',
                    'grupos'        => $grupos,
                    'promedio_ref'  => $promedio,
                    'pct_aprobados' => $pctAprobados,
                    'satisfaccion'  => $satisfaccion,
                ];
            });

        return view('estadisticas.docentes', compact('periodo', 'periodos', 'docentes'));
    }

    // CU27 — Estadísticas por grupo
    public function porGrupo(Request $request)
    {
        $periodo  = $this->periodo($request);
        $periodos = Periodo::orderBy('id', 'desc')->get();

        $grupos = collect();
        if ($periodo) {
            $grupos = Grupo::with([
                    'grupoMaterias.materia',
                    'grupoMaterias.docente.persona',
                    'aula',
                    'horario',
                ])
                ->where('periodo_id', $periodo->id)
                ->orderBy('codigo')
                ->get()
                ->map(function ($g) use ($periodo) {
                    // Calcular promedio y % aprobados del grupo desde notas
                    $stats = DB::table('notas as n')
                        ->join('grupo_materias as gm', 'gm.id', '=', 'n.grupo_materia_id')
                        ->where('gm.grupo_id', $g->id)
                        ->whereNotNull('n.nota_final')
                        ->selectRaw('
                            COUNT(DISTINCT n.postulante_id) as total_postulantes,
                            ROUND(AVG(n.nota_final)::numeric, 2) as promedio,
                            SUM(CASE WHEN n.nota_final >= 51 THEN 1 ELSE 0 END) as aprobados_notas
                        ')
                        ->first();

                    $g->stats_promedio = $stats ? (float) $stats->promedio : null;
                    $g->stats_pct_aprobados = ($stats && $stats->total_postulantes > 0)
                        ? round($stats->aprobados_notas * 100 / ($stats->total_postulantes * 4), 1)
                        : null;

                    return $g;
                });
        }

        return view('estadisticas.grupos', compact('periodo', 'periodos', 'grupos'));
    }
}
