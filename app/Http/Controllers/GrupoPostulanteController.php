<?php

namespace App\Http\Controllers;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Inscripcion;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GrupoPostulanteController extends Controller
{
    // Vista de selección de grupos agrupados por turno
    public function seleccionar(Inscripcion $inscripcion)
    {
        $periodoActivo = Periodo::where('activo', true)->first();

        if (!$periodoActivo) {
            return back()->with('error', 'No hay periodo activo.');
        }

        // Verificar que el postulante tiene pago aprobado
        if (!in_array($inscripcion->estado, ['habilitado', 'pago_aprobado', 'pagado'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Debes completar el pago antes de seleccionar grupo.');
        }

        // Ver si ya tiene grupos asignados
        $gruposAsignados = Grupo::whereHas('postulantes', function ($q) use ($inscripcion) {
                $q->where('postulante_id', $inscripcion->postulante_id);
            })
            ->with(['materia', 'horario', 'aula', 'docente.persona'])
            ->where('periodo_id', $periodoActivo->id)
            ->get();

        // Grupos disponibles agrupados por turno
        $gruposPorTurno = Grupo::with(['materia', 'horario', 'aula', 'docente.persona'])
            ->where('periodo_id', $periodoActivo->id)
            ->where('activo', true)
            ->whereHas('horario')
            ->whereRaw('inscritos_actuales < cupo_max')
            ->get()
            ->groupBy(fn($g) => $g->horario->turno ?? 'Sin turno');

        return view('grupos.seleccionar', compact(
            'inscripcion',
            'gruposPorTurno',
            'gruposAsignados',
            'periodoActivo'
        ));
    }

    // Postulante confirma selección de grupo
    public function confirmar(Request $request, Inscripcion $inscripcion)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
        ]);

        $grupo = Grupo::findOrFail($request->grupo_id);

        // Verificar cupo
        if ($grupo->inscritos_actuales >= $grupo->cupo_max) {
            return back()->with('error', 'El grupo seleccionado ya no tiene cupos disponibles.');
        }

        // Verificar que no esté ya en un grupo de la misma materia
        $yaEnMateria = Grupo::whereHas('postulantes', function ($q) use ($inscripcion) {
                $q->where('postulante_id', $inscripcion->postulante_id);
            })
            ->where('materia_id', $grupo->materia_id)
            ->exists();

        if ($yaEnMateria) {
            return back()->with('error', 'Ya tienes un grupo asignado para esa materia.');
        }

        DB::transaction(function () use ($grupo, $inscripcion) {
            // Asignar postulante al grupo
            $grupo->postulantes()->attach($inscripcion->postulante_id, [
                'fecha_asignacion' => now(),
            ]);

            // Incrementar inscritos
            $grupo->increment('inscritos_actuales');

            BitacoraLogger::registrar(
                'POSTULANTE_GRUPO_SELECCIONADO',
                'Grupos',
                "Postulante #{$inscripcion->postulante_id} seleccionó grupo {$grupo->codigo} ({$grupo->materia->nombre})",
                Auth::id()
            );
        });

        return redirect()->route('grupos.seleccionar', $inscripcion)
            ->with('success', "✅ Te inscribiste correctamente en el grupo {$grupo->codigo} — {$grupo->materia->nombre}.");
    }

    // Postulante abandona un grupo
    public function abandonar(Request $request, Inscripcion $inscripcion, Grupo $grupo)
    {
        $grupo->postulantes()->detach($inscripcion->postulante_id);
        $grupo->decrement('inscritos_actuales');

        BitacoraLogger::registrar(
            'POSTULANTE_GRUPO_ABANDONADO',
            'Grupos',
            "Postulante #{$inscripcion->postulante_id} abandonó grupo {$grupo->codigo}",
            Auth::id()
        );

        return back()->with('success', "Saliste del grupo {$grupo->codigo}.");
    }
}