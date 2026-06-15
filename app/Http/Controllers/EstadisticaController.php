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
        $periodo = $this->periodo($request);
        $periodos = Periodo::orderBy('id', 'desc')->get();

        $porCarrera = collect();
        $aprobReprob = ['aprobados' => 0, 'reprobados' => 0];
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

            // Promedio por materia (DEMO: sin notas por materia aún — depende de CU21-23)
            $globalAvg = (float) ResultadoAdmision::where('periodo_id', $periodo->id)->avg('promedio_final');
            $promedioMateria = Materia::where('activo', true)->orderBy('sigla')->get()
                ->mapWithKeys(fn ($m) => [$m->nombre => round(max(0, min(100, $globalAvg + (($m->id % 5) - 2) * 2.5)), 2)]);
        }

        return view('estadisticas.dashboard', compact('periodo', 'periodos', 'porCarrera', 'aprobReprob', 'promedioMateria'));
    }

    // CU27 — Estadísticas por docente
    public function porDocente(Request $request)
    {
        $periodo = $this->periodo($request);
        $periodos = Periodo::orderBy('id', 'desc')->get();

        $globalAvg = $periodo ? round((float) ResultadoAdmision::where('periodo_id', $periodo->id)->avg('promedio_final'), 2) : 0;
        $globalAprob = 0;
        if ($periodo) {
            $tot = ResultadoAdmision::where('periodo_id', $periodo->id)->count();
            $globalAprob = $tot ? round(ResultadoAdmision::where('periodo_id', $periodo->id)->where('promedio_final', '>=', 51)->count() * 100 / $tot, 1) : 0;
        }

        $docentes = Docente::with('persona', 'profesion')
            ->where('activo', true)
            ->get()
            ->map(function ($d) use ($periodo, $globalAvg, $globalAprob) {
                // Contar grupos-turno donde el docente tiene al menos un bloque de materia.
                // docente_id ahora vive en grupo_materias (pivot), no en grupos.
                $grupos = 0;
                $materiaSigla = null;

                if ($periodo) {
                    $grupos = Grupo::where('periodo_id', $periodo->id)
                        ->whereHas('grupoMaterias', fn ($q) => $q->where('docente_id', $d->id))
                        ->count();

                    // Materia (sigla) que dicta este docente
                    $materiaSigla = $d->materia ?? 'Sin asignar';
                }

                return (object) [
                    'id'            => $d->id,
                    'nombre'        => $d->persona->nombre ?? ('Docente #' . $d->id),
                    'materia'       => $materiaSigla,
                    'profesion'     => $d->profesion->nombre ?? '—',
                    'grupos'        => $grupos,
                    'promedio_ref'  => $grupos > 0 ? $globalAvg : null,   // referencial (sin notas por grupo aún)
                    'pct_aprobados' => $grupos > 0 ? $globalAprob : null,
                ];
            });

        return view('estadisticas.docentes', compact('periodo', 'periodos', 'docentes'));
    }

    // CU27 — Estadísticas por grupo
    public function porGrupo(Request $request)
    {
        $periodo = $this->periodo($request);
        $periodos = Periodo::orderBy('id', 'desc')->get();

        $grupos = collect();
        if ($periodo) {
            // Un grupo es un TURNO completo; materia y docente viven en grupo_materias.
            $grupos = Grupo::with([
                    'grupoMaterias.materia',
                    'grupoMaterias.docente.persona',
                    'aula',
                    'horario',
                ])
                ->where('periodo_id', $periodo->id)
                ->orderBy('codigo')
                ->get();
        }

        return view('estadisticas.grupos', compact('periodo', 'periodos', 'grupos'));
    }
}
