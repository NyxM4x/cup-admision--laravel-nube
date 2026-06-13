<?php

namespace App\Http\Controllers;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Models\Aula;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\GrupoMateria;
use App\Models\Horario;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Postulante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GrupoController extends Controller
{
    public const MAX_GRUPOS_DOCENTE = 4;
    public const CUPO_DEFAULT       = 70;

    // ══════════════════════════════════════════════════════════
    // LISTADO
    // ══════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $q            = trim($request->input('q', ''));
        $periodoActivo = Periodo::where('activo', true)->orderBy('id', 'desc')->first();
        $periodoId    = $request->input('periodo_id', $periodoActivo?->id);
        $periodos     = Periodo::orderBy('id', 'desc')->get();

        $query = Grupo::with([
                'periodo',
                'horario',
                'aula',
                'grupoMaterias.materia',
                'grupoMaterias.docente.persona',
                'grupoMaterias.aula',
            ])
            ->orderBy('horario_id')
            ->orderBy('codigo');

        if ($periodoId && $periodoId !== 'todos') {
            $query->where('periodo_id', $periodoId);
        }

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->whereRaw('unaccent(codigo) ilike unaccent(?)', ["%{$q}%"])
                  ->orWhereHas('horario', fn ($h) =>
                      $h->whereRaw('unaccent(turno) ilike unaccent(?)', ["%{$q}%"])
                  );
            });
        }

        $grupos = $query->paginate(20)->withQueryString();
        $aulas  = Aula::where('activo', true)->orderBy('codigo')->get();

        return view('grupos.index', compact(
            'grupos', 'q', 'periodos', 'periodoId', 'periodoActivo', 'aulas'
        ));
    }

    // ══════════════════════════════════════════════════════════
    // CREAR GRUPO (turno completo con 4 materias)
    // ══════════════════════════════════════════════════════════

    public function create()
    {
        return view('grupos.create', $this->datosFormulario());
    }

    public function store(Request $request)
    {
        // Validar cabecera del grupo
        $data = $request->validate([
            'codigo'     => 'required|string|max:20',
            'periodo_id' => 'required|exists:periodos,id',
            'horario_id' => 'required|exists:horarios,id',
            'aula_id'    => 'nullable|exists:aulas,id',
            'cupo_max'   => 'required|integer|min:1|max:300',
        ]);

        // Validar los 4 bloques de materias
        $request->validate([
            'bloques'                  => 'required|array|min:1',
            'bloques.*.materia_id'     => 'required|exists:materias,id',
            'bloques.*.docente_id'     => 'nullable|exists:docentes,id',
            'bloques.*.hora_inicio'    => 'required|date_format:H:i',
            'bloques.*.hora_fin'       => 'required|date_format:H:i|after:bloques.*.hora_inicio',
            'bloques.*.aula_id'        => 'nullable|exists:aulas,id',
        ]);

        // Verificar unicidad de código en el periodo
        $dup = Grupo::where('periodo_id', $data['periodo_id'])
            ->where('codigo', $data['codigo'])->exists();
        if ($dup) {
            return back()->withInput()
                ->withErrors(['codigo' => 'Ya existe un grupo con ese código en este periodo.']);
        }

        // Verificar que no se repita materia en los bloques
        $materias = collect($request->bloques)->pluck('materia_id');
        if ($materias->unique()->count() !== $materias->count()) {
            return back()->withInput()
                ->withErrors(['bloques' => 'No puede repetir la misma materia en dos bloques.']);
        }

        // Validar límite docente (máx 4 grupos por periodo por cada docente)
        if ($error = $this->validarLimiteDocentes($request->bloques, $data['periodo_id'])) {
            return back()->withInput()->withErrors($error);
        }

        DB::transaction(function () use ($data, $request) {
            $grupo = Grupo::create($data + [
                'inscritos_actuales' => 0,
                'activo'             => true,
            ]);

            foreach ($request->bloques as $orden => $bloque) {
                GrupoMateria::create([
                    'grupo_id'    => $grupo->id,
                    'materia_id'  => $bloque['materia_id'],
                    'docente_id'  => $bloque['docente_id'] ?? null,
                    'hora_inicio' => $bloque['hora_inicio'],
                    'hora_fin'    => $bloque['hora_fin'],
                    'aula_id'     => $bloque['aula_id'] ?? null,
                    'orden'       => $orden + 1,
                ]);
            }

            BitacoraLogger::registrar(
                'GRUPO_CREADO',
                'Grupos',
                "Grupo {$grupo->codigo} creado (turno: {$grupo->horario?->turno}) — periodo #{$grupo->periodo_id}",
                Auth::id()
            );
        });

        return redirect()->route('grupos.index', ['periodo_id' => $data['periodo_id']])
            ->with('success', "Grupo '{$data['codigo']}' creado correctamente.");
    }

    // ══════════════════════════════════════════════════════════
    // EDITAR GRUPO
    // ══════════════════════════════════════════════════════════

    public function edit(Grupo $grupo)
    {
        $grupo->load('grupoMaterias.materia', 'grupoMaterias.docente', 'grupoMaterias.aula');
        return view('grupos.edit', $this->datosFormulario() + compact('grupo'));
    }

    public function update(Request $request, Grupo $grupo)
    {
        $data = $request->validate([
            'codigo'     => 'required|string|max:20',
            'horario_id' => 'required|exists:horarios,id',
            'aula_id'    => 'nullable|exists:aulas,id',
            'cupo_max'   => 'required|integer|min:1|max:300',
        ]);

        $request->validate([
            'bloques'                  => 'required|array|min:1',
            'bloques.*.materia_id'     => 'required|exists:materias,id',
            'bloques.*.docente_id'     => 'nullable|exists:docentes,id',
            'bloques.*.hora_inicio'    => 'required|date_format:H:i',
            'bloques.*.hora_fin'       => 'required|date_format:H:i|after:bloques.*.hora_inicio',
            'bloques.*.aula_id'        => 'nullable|exists:aulas,id',
        ]);

        $materias = collect($request->bloques)->pluck('materia_id');
        if ($materias->unique()->count() !== $materias->count()) {
            return back()->withInput()
                ->withErrors(['bloques' => 'No puede repetir la misma materia en dos bloques.']);
        }

        if ($error = $this->validarLimiteDocentes($request->bloques, $grupo->periodo_id, $grupo->id)) {
            return back()->withInput()->withErrors($error);
        }

        DB::transaction(function () use ($data, $request, $grupo) {
            $grupo->update($data);

            // Reemplazar todos los bloques (delete + recrear — tabla vacía en producción)
            $grupo->grupoMaterias()->delete();

            foreach ($request->bloques as $orden => $bloque) {
                GrupoMateria::create([
                    'grupo_id'    => $grupo->id,
                    'materia_id'  => $bloque['materia_id'],
                    'docente_id'  => $bloque['docente_id'] ?? null,
                    'hora_inicio' => $bloque['hora_inicio'],
                    'hora_fin'    => $bloque['hora_fin'],
                    'aula_id'     => $bloque['aula_id'] ?? null,
                    'orden'       => $orden + 1,
                ]);
            }

            BitacoraLogger::registrar(
                'GRUPO_EDITADO',
                'Grupos',
                "Grupo {$grupo->codigo} editado",
                Auth::id()
            );
        });

        return redirect()->route('grupos.index', ['periodo_id' => $grupo->periodo_id])
            ->with('success', "Grupo '{$grupo->codigo}' actualizado.");
    }

    // ══════════════════════════════════════════════════════════
    // ARCHIVAR / REACTIVAR
    // ══════════════════════════════════════════════════════════

    public function archivar(Grupo $grupo)
    {
        $grupo->update(['activo' => false]);
        BitacoraLogger::registrar('GRUPO_ARCHIVADO', 'Grupos', "Grupo {$grupo->codigo} archivado", Auth::id());
        return back()->with('success', "Grupo '{$grupo->codigo}' archivado.");
    }

    public function reactivar(Grupo $grupo)
    {
        $grupo->update(['activo' => true]);
        BitacoraLogger::registrar('GRUPO_REACTIVADO', 'Grupos', "Grupo {$grupo->codigo} reactivado", Auth::id());
        return back()->with('success', "Grupo '{$grupo->codigo}' reactivado.");
    }

    // ══════════════════════════════════════════════════════════
    // GENERACIÓN AUTOMÁTICA (CU17)
    // Genera grupos vacíos (sin bloques) por turno; el admin los configura luego
    // ══════════════════════════════════════════════════════════

    public function formGenerar()
    {
        $periodo = Periodo::where('activo', true)->orderBy('id', 'desc')->first();

        $habilitados      = 0;
        $gruposPorTurno   = 0;
        $turnosDisponibles = Horario::where('activo', true)->count();

        if ($periodo) {
            $habilitados    = Postulante::where('activo', true)
                ->whereHas('inscripciones', fn ($w) => $w->where('periodo_id', $periodo->id))
                ->count();
            // Necesitamos ceil(habilitados/70) grupos por cada turno
            $gruposPorTurno = max(1, (int) ceil($habilitados / self::CUPO_DEFAULT));
        }

        $existentes = $periodo ? Grupo::where('periodo_id', $periodo->id)->count() : 0;

        return view('grupos.generar-automaticos', compact(
            'periodo', 'habilitados', 'gruposPorTurno', 'turnosDisponibles', 'existentes'
        ));
    }

    public function generarAutomaticos(Request $request)
    {
        $periodo = Periodo::where('activo', true)->orderBy('id', 'desc')->first();
        if (! $periodo) {
            return back()->withErrors(['general' => 'No hay un periodo activo.']);
        }

        $horarios = Horario::where('activo', true)->orderBy('hora_inicio')->get();
        if ($horarios->isEmpty()) {
            return back()->withErrors(['general' => 'No hay horarios activos. Crea primero los turnos Mañana y Tarde.']);
        }

        $habilitados    = Postulante::where('activo', true)
            ->whereHas('inscripciones', fn ($w) => $w->where('periodo_id', $periodo->id))
            ->count();

        $gruposPorTurno = max(1, (int) ceil($habilitados / self::CUPO_DEFAULT));
        $creados        = 0;

        DB::transaction(function () use ($periodo, $horarios, $gruposPorTurno, &$creados) {
            foreach ($horarios as $horario) {
                $existentes = Grupo::where('periodo_id', $periodo->id)
                    ->where('horario_id', $horario->id)->count();

                for ($n = $existentes + 1; $n <= $gruposPorTurno; $n++) {
                    Grupo::create([
                        'codigo'             => "G-{$periodo->id}-{$horario->codigo}-{$n}",
                        'periodo_id'         => $periodo->id,
                        'horario_id'         => $horario->id,
                        'cupo_max'           => self::CUPO_DEFAULT,
                        'inscritos_actuales' => 0,
                        'activo'             => true,
                    ]);
                    $creados++;
                }
            }

            BitacoraLogger::registrar(
                'GRUPOS_GENERADOS_AUTO',
                'Grupos',
                "Generación automática periodo #{$periodo->id}: {$creados} grupos nuevos",
                Auth::id()
            );
        });

        $msg = $creados > 0
            ? "Se generaron {$creados} grupos ({$gruposPorTurno} por turno × {$horarios->count()} turnos). Ahora configura las materias de cada grupo."
            : 'No se generaron grupos nuevos: ya existían suficientes.';

        return redirect()->route('grupos.index', ['periodo_id' => $periodo->id])
            ->with('success', $msg);
    }

    // ══════════════════════════════════════════════════════════
    // AJAX: Docentes filtrados por sigla de materia
    // ══════════════════════════════════════════════════════════

    public function docentesPorMateria(Request $request)
    {
        $materiaId = $request->input('materia_id');

        if (! $materiaId) {
            return response()->json([]);
        }

        $materia = Materia::find($materiaId);
        if (! $materia) {
            return response()->json([]);
        }

        // Filtrar docentes cuya columna 'materia' coincide con la sigla
        $docentes = Docente::with('persona')
            ->where('activo', true)
            ->where('materia', $materia->sigla)
            ->orderBy('id')
            ->get()
            ->map(fn ($d) => [
                'id'     => $d->id,
                'nombre' => $d->persona?->nombre ?? 'Docente #' . $d->id,
            ]);

        return response()->json($docentes);
    }

    // ══════════════════════════════════════════════════════════
    // ASIGNAR AULA AL GRUPO (acceso rápido desde listado)
    // ══════════════════════════════════════════════════════════

    public function asignarAula(Request $request, Grupo $grupo)
    {
        $request->validate(['aula_id' => 'required|exists:aulas,id']);

        $grupo->update(['aula_id' => $request->aula_id]);

        $aula = Aula::find($request->aula_id);
        BitacoraLogger::registrar(
            'AULA_ASIGNADA_GRUPO',
            'Grupos',
            "Aula {$aula?->codigo} asignada al grupo {$grupo->codigo} (aula por defecto)",
            Auth::id()
        );

        return back()->with('success', 'Aula por defecto asignada al grupo.');
    }

    // ══════════════════════════════════════════════════════════
    // HELPERS PRIVADOS
    // ══════════════════════════════════════════════════════════

    private function datosFormulario(): array
    {
        return [
            'periodos' => Periodo::orderBy('id', 'desc')->get(),
            'horarios' => Horario::where('activo', true)->orderBy('hora_inicio')->get(),
            'aulas'    => Aula::where('activo', true)->orderBy('codigo')->get(),
            'materias' => Materia::where('activo', true)->orderBy('sigla')->get(),
            // Docentes organizados por sigla de materia para el JS inicial
            'docentesPorMateria' => Docente::with('persona')
                ->where('activo', true)
                ->get()
                ->groupBy('materia')
                ->map(fn ($grp) => $grp->map(fn ($d) => [
                    'id'     => $d->id,
                    'nombre' => $d->persona?->nombre ?? 'Docente #' . $d->id,
                ])),
        ];
    }

    /**
     * Valida que ningún docente en los bloques supere el límite de 4 grupos en el periodo.
     * Devuelve array de errores o null si todo OK.
     */
    private function validarLimiteDocentes(array $bloques, int $periodoId, ?int $ignoreGrupoId = null): ?array
    {
        $docenteIds = collect($bloques)
            ->pluck('docente_id')
            ->filter()
            ->unique();

        foreach ($docenteIds as $docenteId) {
            // Contar grupos donde este docente tiene al menos un bloque en el periodo
            $cuenta = GrupoMateria::whereHas('grupo', function ($q) use ($periodoId, $ignoreGrupoId) {
                    $q->where('periodo_id', $periodoId);
                    if ($ignoreGrupoId) {
                        $q->where('id', '!=', $ignoreGrupoId);
                    }
                })
                ->where('docente_id', $docenteId)
                ->distinct('grupo_id')
                ->count('grupo_id');

            if ($cuenta >= self::MAX_GRUPOS_DOCENTE) {
                $docente = Docente::with('persona')->find($docenteId);
                $nombre  = $docente?->persona?->nombre ?? "ID #{$docenteId}";
                return ['bloques' => "El docente {$nombre} ya tiene " . self::MAX_GRUPOS_DOCENTE . " grupos en este periodo (máximo permitido)."];
            }
        }

        return null;
    }
}