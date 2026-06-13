<?php

namespace App\Http\Controllers;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Models\Grupo;
use App\Models\GrupoMateria;
use App\Models\Nota;
use App\Models\Periodo;
use App\Models\Postulante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * CU21 — Registrar notas de exámenes
 * CU22 — Calcular nota final y resultado
 *
 * Flujo:
 *  Admin/Docente entra a /notas → selecciona grupo → ve tabla de postulantes
 *  por cada bloque (materia) y registra las 3 notas. Al guardar, CU22
 *  calcula automáticamente la nota final y el resultado.
 */
class NotaController extends Controller
{
    // ══════════════════════════════════════════════════════
    // INDEX — Seleccionar grupo para gestionar notas
    // ══════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $periodoActivo = Periodo::where('activo', true)->orderBy('id', 'desc')->first();
        $periodoId     = $request->input('periodo_id', $periodoActivo?->id);
        $periodos      = Periodo::orderBy('id', 'desc')->get();

        $grupos = Grupo::with(['horario', 'grupoMaterias.materia'])
            ->where('activo', true)
            ->when($periodoId && $periodoId !== 'todos', fn ($q) =>
                $q->where('periodo_id', $periodoId)
            )
            ->orderBy('horario_id')
            ->orderBy('codigo')
            ->get();

        return view('notas.index', compact('grupos', 'periodos', 'periodoId', 'periodoActivo'));
    }

    // ══════════════════════════════════════════════════════
    // SHOW — Tabla de notas de un grupo completo
    // Docente/Admin ve todas las materias + todos los postulantes
    // ══════════════════════════════════════════════════════

    public function show(Grupo $grupo)
    {
        $grupo->load([
            'horario',
            'grupoMaterias' => fn ($q) => $q->orderBy('orden'),
            'grupoMaterias.materia',
            'grupoMaterias.docente.persona',
            'postulantes.persona',
        ]);

        // Para cada bloque de materia, cargar las notas existentes
        // indexadas por postulante_id para acceso rápido en la vista
        $notasPorBloque = [];
        foreach ($grupo->grupoMaterias as $gm) {
            $notasPorBloque[$gm->id] = Nota::where('grupo_materia_id', $gm->id)
                ->get()
                ->keyBy('postulante_id');
        }

        return view('notas.show', compact('grupo', 'notasPorBloque'));
    }

    // ══════════════════════════════════════════════════════
    // STORE — CU21: Guardar notas de UN postulante en UN bloque
    // Llamado por AJAX desde la vista show (fila inline)
    // ══════════════════════════════════════════════════════

    public function store(Request $request)
    {
        $data = $request->validate([
            'grupo_materia_id' => 'required|exists:grupo_materias,id',
            'postulante_id'    => 'required|exists:postulantes,id',
            'examen1'          => 'nullable|numeric|min:0|max:100',
            'examen2'          => 'nullable|numeric|min:0|max:100',
            'examen3'          => 'nullable|numeric|min:0|max:100',
            'observacion'      => 'nullable|string|max:500',
        ]);

        // Verificar que el postulante pertenece al grupo de ese bloque
        $gm    = GrupoMateria::findOrFail($data['grupo_materia_id']);
        $grupo = $gm->grupo;

        $perteneceAlGrupo = $grupo->postulantes()
            ->where('postulante_id', $data['postulante_id'])
            ->exists();

        if (! $perteneceAlGrupo) {
            return response()->json([
                'error' => 'El postulante no pertenece a este grupo.'
            ], 422);
        }

        DB::transaction(function () use ($data, $gm) {
            // CU21: Crear o actualizar la nota
            $nota = Nota::updateOrCreate(
                [
                    'grupo_materia_id' => $data['grupo_materia_id'],
                    'postulante_id'    => $data['postulante_id'],
                ],
                [
                    'examen1'       => $data['examen1'] ?? null,
                    'examen2'       => $data['examen2'] ?? null,
                    'examen3'       => $data['examen3'] ?? null,
                    'observacion'   => $data['observacion'] ?? null,
                    'registrado_por'=> Auth::id(),
                ]
            );

            // CU22: Calcular nota final y resultado automáticamente
            $nota->calcularYGuardar();

            BitacoraLogger::registrar(
                'NOTA_REGISTRADA',
                'Notas',
                "Nota registrada: postulante #{$data['postulante_id']} — bloque #{$data['grupo_materia_id']} — final: {$nota->nota_final} ({$nota->resultado})",
                Auth::id()
            );
        });

        $nota = Nota::where('grupo_materia_id', $data['grupo_materia_id'])
            ->where('postulante_id', $data['postulante_id'])
            ->first();

        return response()->json([
            'success'    => true,
            'nota_final' => $nota->nota_formateada,
            'resultado'  => $nota->resultado,
            'badge'      => $nota->badge_resultado,
        ]);
    }

    // ══════════════════════════════════════════════════════
    // STORE MASIVO — Guardar todas las notas de un bloque de una vez
    // Para cuando el docente llena la planilla completa
    // ══════════════════════════════════════════════════════

    public function storeMasivo(Request $request, GrupoMateria $grupoMateria)
    {
        $request->validate([
            'notas'                => 'required|array',
            'notas.*.postulante_id'=> 'required|exists:postulantes,id',
            'notas.*.examen1'      => 'nullable|numeric|min:0|max:100',
            'notas.*.examen2'      => 'nullable|numeric|min:0|max:100',
            'notas.*.examen3'      => 'nullable|numeric|min:0|max:100',
        ]);

        $guardadas = 0;

        DB::transaction(function () use ($request, $grupoMateria, &$guardadas) {
            foreach ($request->notas as $item) {
                $nota = Nota::updateOrCreate(
                    [
                        'grupo_materia_id' => $grupoMateria->id,
                        'postulante_id'    => $item['postulante_id'],
                    ],
                    [
                        'examen1'        => $item['examen1'] ?? null,
                        'examen2'        => $item['examen2'] ?? null,
                        'examen3'        => $item['examen3'] ?? null,
                        'registrado_por' => Auth::id(),
                    ]
                );

                // CU22: calcular automáticamente
                $nota->calcularYGuardar();
                $guardadas++;
            }

            BitacoraLogger::registrar(
                'NOTAS_MASIVAS',
                'Notas',
                "Notas masivas registradas: bloque #{$grupoMateria->id} ({$grupoMateria->materia?->sigla}) — {$guardadas} postulantes",
                Auth::id()
            );
        });

        return back()->with('success', "✅ {$guardadas} notas registradas y calculadas correctamente.");
    }
}