<?php

namespace App\Http\Controllers;

use App\Exports\GenericExport;
use App\Models\Bitacora;
use App\Models\Grupo;
use App\Models\Inscripcion;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\ResultadoAdmision;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public const REPORTES = [
        'inscritos'              => 'Inscritos del periodo',
        'aprobados_reprobados'   => 'Aprobados y reprobados',
        'admitidos_por_carrera'  => 'Admitidos por carrera',
        'promedios_por_materia'  => 'Promedios por materia',
        'bitacora'               => 'Bitácora del sistema',
    ];

    public function index()
    {
        $reportes = self::REPORTES;

        return view('reportes.index', compact('reportes'));
    }

    public function html(string $tipo)
    {
        $data = $this->construir($tipo);

        return view('reportes.html', $data);
    }

    public function pdf(string $tipo)
    {
        $data = $this->construir($tipo);

        $pdf = Pdf::loadView('reportes.pdf.generico', $data)->setPaper('a4', 'portrait');

        return $pdf->download("reporte_{$tipo}.pdf");
    }

    public function excel(string $tipo)
    {
        $data = $this->construir($tipo);

        return Excel::download(
            new GenericExport($data['rows'], $data['encabezados']),
            "reporte_{$tipo}.xlsx"
        );
    }

    // Devuelve titulo, encabezados y filas según el tipo de reporte
    private function construir(string $tipo): array
    {
        abort_unless(array_key_exists($tipo, self::REPORTES), 404);

        $periodo = Periodo::where('activo', true)->orderBy('id', 'desc')->first();
        $titulo = self::REPORTES[$tipo];
        $encabezados = [];
        $rows = [];

        switch ($tipo) {
            case 'inscritos':
                $encabezados = ['#', 'CI', 'Nombre', 'Colegio', '1ra Carrera', 'Estado'];
                $insc = Inscripcion::with(['postulante.persona', 'postulacionCarreras.carrera'])
                    ->where('periodo_id', $periodo?->id)
                    ->where('estado', 'activa')->orderBy('id')->get();
                foreach ($insc as $i => $row) {
                    $c1 = $row->postulacionCarreras->firstWhere('prioridad', 1);
                    $rows[] = [
                        $i + 1,
                        $row->postulante->persona->ci ?? '',
                        $row->postulante->persona->nombre ?? '',
                        $row->postulante->colegio ?? '',
                        optional(optional($c1)->carrera)->nombre ?? '—',
                        ucfirst($row->postulante->estado ?? ''),
                    ];
                }
                break;

            case 'aprobados_reprobados':
                $encabezados = ['Ranking', 'CI', 'Nombre', 'Promedio', 'Estado'];
                $res = ResultadoAdmision::with('postulante.persona')
                    ->where('periodo_id', $periodo?->id)
                    ->orderByDesc('promedio_final')->get();
                foreach ($res as $i => $r) {
                    $rows[] = [
                        $i + 1,
                        $r->postulante->persona->ci ?? '',
                        $r->postulante->persona->nombre ?? '',
                        number_format($r->promedio_final, 2),
                        $r->promedio_final >= 51 ? 'Aprobado' : 'Reprobado',
                    ];
                }
                break;

            case 'admitidos_por_carrera':
                $encabezados = ['Carrera', 'CI', 'Nombre', 'Promedio', 'Opción'];
                $res = ResultadoAdmision::with(['postulante.persona', 'carreraAsignada'])
                    ->where('periodo_id', $periodo?->id)
                    ->whereIn('estado_admision', ['admitido_primera', 'admitido_segunda'])
                    ->orderBy('carrera_asignada_id')->orderByDesc('promedio_final')->get();
                foreach ($res as $r) {
                    $rows[] = [
                        optional($r->carreraAsignada)->nombre ?? '—',
                        $r->postulante->persona->ci ?? '',
                        $r->postulante->persona->nombre ?? '',
                        number_format($r->promedio_final, 2),
                        $r->estado_admision === 'admitido_primera' ? '1ra' : '2da',
                    ];
                }
                break;

            case 'promedios_por_materia':
                $encabezados = ['Materia', 'Sigla', 'Grupos', 'Promedio (demo)'];
                $globalAvg = (float) ResultadoAdmision::where('periodo_id', $periodo?->id)->avg('promedio_final');
                foreach (Materia::where('activo', true)->orderBy('sigla')->get() as $m) {
                    $grupos = Grupo::where('periodo_id', $periodo?->id)->where('materia_id', $m->id)->count();
                    $rows[] = [
                        $m->nombre,
                        $m->sigla,
                        $grupos,
                        number_format(max(0, min(100, $globalAvg + (($m->id % 5) - 2) * 2.5)), 2),
                    ];
                }
                break;

            case 'bitacora':
                $encabezados = ['Fecha', 'Acción', 'Módulo', 'Descripción'];
                $bits = Bitacora::orderByDesc('created_at')->limit(500)->get();
                foreach ($bits as $b) {
                    $rows[] = [
                        optional($b->created_at)->format('d/m/Y H:i'),
                        $b->accion,
                        $b->modulo,
                        $b->descripcion,
                    ];
                }
                break;
        }

        return compact('titulo', 'encabezados', 'rows', 'periodo', 'tipo');
    }
}
