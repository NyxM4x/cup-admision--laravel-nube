<?php

namespace App\Http\Controllers;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Models\Aula;
use App\Models\Docente;
use App\Models\Grupo;
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
    public const CUPO_DEFAULT = 80;

    public function index(Request $request)
    {
        $q = trim($request->input('q', ''));

        $periodoActivo = Periodo::where('activo', true)->orderBy('id', 'desc')->first();
        $periodoId = $request->input('periodo_id', $periodoActivo?->id);
        $periodos = Periodo::orderBy('id', 'desc')->get();

        $query = Grupo::with(['periodo', 'materia', 'horario', 'aula', 'docente.persona'])
            ->orderBy('materia_id')
            ->orderBy('codigo');

        if ($periodoId && $periodoId !== 'todos') {
            $query->where('periodo_id', $periodoId);
        }

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->whereRaw('unaccent(codigo) ilike unaccent(?)', ["%{$q}%"])
                  ->orWhereHas('materia', fn ($m) => $m->whereRaw('unaccent(nombre) ilike unaccent(?)', ["%{$q}%"])
                                                       ->orWhereRaw('unaccent(sigla) ilike unaccent(?)', ["%{$q}%"]));
            });
        }

        $grupos = $query->paginate(30)->withQueryString();

        // Para asignación inline de docente/aula
        $docentes = Docente::with('persona')->where('activo', true)->get();
        $aulas = Aula::where('activo', true)->orderBy('codigo')->get();

        return view('grupos.index', compact('grupos', 'q', 'periodos', 'periodoId', 'periodoActivo', 'docentes', 'aulas'));
    }

    public function create()
    {
        return view('grupos.create', $this->datosFormulario());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo'     => 'required|string|max:20',
            'periodo_id' => 'required|exists:periodos,id',
            'materia_id' => 'required|exists:materias,id',
            'horario_id' => 'nullable|exists:horarios,id',
            'aula_id'    => 'nullable|exists:aulas,id',
            'docente_id' => 'nullable|exists:docentes,id',
            'cupo_max'   => 'required|integer|min:1|max:300',
        ]);

        // Unicidad codigo dentro de periodo+materia
        $dup = Grupo::where('periodo_id', $data['periodo_id'])
            ->where('materia_id', $data['materia_id'])
            ->where('codigo', $data['codigo'])->exists();
        if ($dup) {
            return back()->withInput()->withErrors(['codigo' => 'Ya existe un grupo con ese código para esa materia/periodo.']);
        }

        if ($error = $this->validarReglas($data)) {
            return back()->withInput()->withErrors($error);
        }

        $grupo = Grupo::create($data + ['inscritos_actuales' => 0, 'activo' => true]);

        BitacoraLogger::registrar(
            'GRUPO_CREADO',
            'Grupos',
            "Grupo {$grupo->codigo} creado (periodo #{$grupo->periodo_id})",
            Auth::id()
        );

        return redirect()->route('grupos.index', ['periodo_id' => $grupo->periodo_id])
            ->with('success', "Grupo '{$grupo->codigo}' creado correctamente.");
    }

    public function edit(Grupo $grupo)
    {
        return view('grupos.edit', $this->datosFormulario() + compact('grupo'));
    }

    public function update(Request $request, Grupo $grupo)
    {
        $data = $request->validate([
            'codigo'     => 'required|string|max:20',
            'horario_id' => 'nullable|exists:horarios,id',
            'aula_id'    => 'nullable|exists:aulas,id',
            'docente_id' => 'nullable|exists:docentes,id',
            'cupo_max'   => 'required|integer|min:1|max:300',
        ]);

        $data['periodo_id'] = $grupo->periodo_id;
        $data['materia_id'] = $grupo->materia_id;

        if ($error = $this->validarReglas($data, $grupo->id)) {
            return back()->withInput()->withErrors($error);
        }

        $grupo->update($data);

        BitacoraLogger::registrar(
            'GRUPO_EDITADO',
            'Grupos',
            "Grupo {$grupo->codigo} editado",
            Auth::id()
        );

        return redirect()->route('grupos.index', ['periodo_id' => $grupo->periodo_id])
            ->with('success', "Grupo '{$grupo->codigo}' actualizado.");
    }

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

    // Pantalla de generación automática (resumen + botón)
    public function formGenerar()
    {
        $periodo = Periodo::where('activo', true)->orderBy('id', 'desc')->first();

        $habilitados = 0;
        $gruposPorMateria = 0;
        $materiasCount = Materia::where('activo', true)->count();

        if ($periodo) {
            $habilitados = Postulante::where('activo', true)
                ->whereHas('inscripciones', fn ($w) => $w->where('periodo_id', $periodo->id))
                ->count();
            $gruposPorMateria = max(1, (int) ceil($habilitados / self::CUPO_DEFAULT));
        }

        $existentes = $periodo ? Grupo::where('periodo_id', $periodo->id)->count() : 0;

        return view('grupos.generar-automaticos', compact(
            'periodo', 'habilitados', 'gruposPorMateria', 'materiasCount', 'existentes'
        ));
    }

    // CU17 — Generación automática de grupos: CEIL(habilitados/80) por materia
    public function generarAutomaticos(Request $request)
    {
        $periodo = Periodo::where('activo', true)->orderBy('id', 'desc')->first();
        if (! $periodo) {
            return back()->withErrors(['general' => 'No hay un periodo activo para generar grupos.']);
        }

        $habilitados = Postulante::where('activo', true)
            ->whereHas('inscripciones', fn ($w) => $w->where('periodo_id', $periodo->id))
            ->count();

        $gruposPorMateria = max(1, (int) ceil($habilitados / self::CUPO_DEFAULT));
        $materias = Materia::where('activo', true)->orderBy('sigla')->get();

        if ($materias->isEmpty()) {
            return back()->withErrors(['general' => 'No hay materias activas para generar grupos.']);
        }

        $creados = 0;

        DB::transaction(function () use ($periodo, $materias, $gruposPorMateria, &$creados) {
            foreach ($materias as $materia) {
                // Idempotente: solo completa hasta el objetivo (no duplica)
                $existentes = Grupo::where('periodo_id', $periodo->id)
                    ->where('materia_id', $materia->id)->count();

                for ($n = $existentes + 1; $n <= $gruposPorMateria; $n++) {
                    Grupo::create([
                        'codigo'             => "G-{$periodo->id}-{$materia->sigla}-{$n}",
                        'periodo_id'         => $periodo->id,
                        'materia_id'         => $materia->id,
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
                "Generación automática periodo #{$periodo->id}: {$creados} grupos nuevos "
                    ."({$gruposPorMateria} por materia × {$materias->count()} materias)",
                Auth::id()
            );
        });

        $msg = $creados > 0
            ? "Se generaron {$creados} grupos ({$gruposPorMateria} por materia)."
            : 'No se generaron grupos nuevos: ya existían suficientes para este periodo.';

        return redirect()->route('grupos.index', ['periodo_id' => $periodo->id])->with('success', $msg);
    }

    // CU18 — Asignar docente (máx 4 grupos por docente en el periodo)
    public function asignarDocente(Request $request, Grupo $grupo)
    {
        $request->validate(['docente_id' => 'required|exists:docentes,id']);
        $docenteId = (int) $request->docente_id;

        $cuenta = Grupo::where('periodo_id', $grupo->periodo_id)
            ->where('docente_id', $docenteId)
            ->where('id', '!=', $grupo->id)
            ->count();

        if ($cuenta >= self::MAX_GRUPOS_DOCENTE) {
            return back()->withErrors([
                'docente_id' => 'El docente ya tiene '.self::MAX_GRUPOS_DOCENTE.' grupos en este periodo (máximo permitido).',
            ]);
        }

        $grupo->update(['docente_id' => $docenteId]);

        $docente = Docente::with('persona')->find($docenteId);
        BitacoraLogger::registrar(
            'DOCENTE_ASIGNADO',
            'Grupos',
            "Docente {$docente?->persona?->nombre} asignado al grupo {$grupo->codigo}",
            Auth::id()
        );

        return back()->with('success', 'Docente asignado al grupo correctamente.');
    }

    // CU20 — Asignar aula (cupo ≤ capacidad y sin choque aula+horario)
    public function asignarAula(Request $request, Grupo $grupo)
    {
        $request->validate(['aula_id' => 'required|exists:aulas,id']);
        $aulaId = (int) $request->aula_id;

        if ($error = $this->validarAula($aulaId, $grupo->cupo_max, $grupo->horario_id, $grupo->periodo_id, $grupo->id)) {
            return back()->withErrors(['aula_id' => $error]);
        }

        $grupo->update(['aula_id' => $aulaId]);

        $aula = Aula::find($aulaId);
        BitacoraLogger::registrar(
            'AULA_ASIGNADA',
            'Grupos',
            "Aula {$aula?->codigo} asignada al grupo {$grupo->codigo}",
            Auth::id()
        );

        return back()->with('success', 'Aula asignada al grupo correctamente.');
    }

    // ── Helpers ──────────────────────────────────────────────

    private function datosFormulario(): array
    {
        return [
            'periodos' => Periodo::orderBy('id', 'desc')->get(),
            'materias' => Materia::where('activo', true)->orderBy('sigla')->get(),
            'horarios' => Horario::where('activo', true)->orderBy('hora_inicio')->get(),
            'aulas'    => Aula::where('activo', true)->orderBy('codigo')->get(),
            'docentes' => Docente::with('persona')->where('activo', true)->get(),
        ];
    }

    // Valida cupo vs capacidad, límite de docente y choque aula+horario. Devuelve array de errores o null.
    private function validarReglas(array $data, ?int $ignoreId = null): ?array
    {
        // Docente: máximo 4 grupos por periodo
        if (! empty($data['docente_id'])) {
            $cuenta = Grupo::where('periodo_id', $data['periodo_id'])
                ->where('docente_id', $data['docente_id'])
                ->when($ignoreId, fn ($w) => $w->where('id', '!=', $ignoreId))
                ->count();
            if ($cuenta >= self::MAX_GRUPOS_DOCENTE) {
                return ['docente_id' => 'El docente ya tiene '.self::MAX_GRUPOS_DOCENTE.' grupos en este periodo.'];
            }
        }

        // Aula: capacidad y choque con horario
        if (! empty($data['aula_id'])) {
            $error = $this->validarAula(
                (int) $data['aula_id'],
                (int) $data['cupo_max'],
                $data['horario_id'] ?? null,
                (int) $data['periodo_id'],
                $ignoreId
            );
            if ($error) {
                return ['aula_id' => $error];
            }
        }

        return null;
    }

    private function validarAula(int $aulaId, int $cupoMax, $horarioId, int $periodoId, ?int $ignoreId = null): ?string
    {
        $aula = Aula::find($aulaId);
        if ($aula && $cupoMax > $aula->capacidad) {
            return "El cupo ({$cupoMax}) supera la capacidad del aula {$aula->codigo} ({$aula->capacidad}).";
        }

        // Choque: misma aula + mismo horario en el mismo periodo (grupos activos)
        if ($horarioId) {
            $choca = Grupo::where('periodo_id', $periodoId)
                ->where('aula_id', $aulaId)
                ->where('horario_id', $horarioId)
                ->where('activo', true)
                ->when($ignoreId, fn ($w) => $w->where('id', '!=', $ignoreId))
                ->exists();
            if ($choca) {
                return 'Esa aula ya está ocupada en ese horario (choque de aula/horario).';
            }
        }

        return null;
    }
}
