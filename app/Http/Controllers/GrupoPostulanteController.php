<?php

namespace App\Http\Controllers;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Models\Grupo;
use App\Models\Inscripcion;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GrupoPostulanteController extends Controller
{
    public function seleccionar(Inscripcion $inscripcion)
    {
        $periodoActivo = Periodo::where('activo', true)->first();

        if (!$periodoActivo) {
            return redirect()->route('dashboard')->with('error', 'No hay periodo activo.');
        }

        if (!in_array($inscripcion->estado, ['habilitado', 'pago_aprobado', 'pagado'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Debes completar el pago antes de seleccionar tu turno.');
        }

        $with = ['horario', 'aula', 'grupoMaterias.materia', 'grupoMaterias.docente.persona', 'grupoMaterias.aula'];

        // ¿Ya tiene turno asignado?
        $grupoAsignado = Grupo::whereHas('postulantes', function ($q) use ($inscripcion) {
                $q->where('postulante_id', $inscripcion->postulante_id);
            })
            ->with($with)
            ->where('periodo_id', $periodoActivo->id)
            ->first();

        if ($grupoAsignado) {
            return view('grupos.seleccionar', [
                'inscripcion'       => $inscripcion,
                'grupoAsignado'     => $grupoAsignado,
                'gruposDisponibles' => collect(),
            ]);
        }

        // Turnos disponibles (con sus 4 materias completas y cupo libre)
        $gruposDisponibles = Grupo::with($with)
            ->where('periodo_id', $periodoActivo->id)
            ->where('activo', true)
            ->whereRaw('inscritos_actuales < cupo_max')
            ->get()
            ->filter(fn ($g) => $g->grupoMaterias->count() >= 4)
            ->groupBy(fn ($g) => $g->horario->turno ?? 'Sin turno');

        return view('grupos.seleccionar', [
            'inscripcion'       => $inscripcion,
            'grupoAsignado'     => null,
            'gruposDisponibles' => $gruposDisponibles,
        ]);
    }

    public function confirmar(Request $request, Inscripcion $inscripcion)
    {
        $request->validate(['grupo_id' => 'required|exists:grupos,id']);

        // Ya tiene turno? no permitir cambiar
        $yaTiene = Grupo::whereHas('postulantes', function ($q) use ($inscripcion) {
                $q->where('postulante_id', $inscripcion->postulante_id);
            })->exists();

        if ($yaTiene) {
            return redirect()->route('grupos.seleccionar', $inscripcion)
                ->with('error', 'Ya tienes un turno asignado. No es posible cambiarlo.');
        }

        $grupo = Grupo::findOrFail($request->grupo_id);

        if ($grupo->inscritos_actuales >= $grupo->cupo_max) {
            return back()->with('error', 'Ese turno ya no tiene cupos disponibles.');
        }

        DB::transaction(function () use ($grupo, $inscripcion) {
            $grupo->postulantes()->attach($inscripcion->postulante_id, [
                'fecha_asignacion' => now(),
            ]);
            $grupo->increment('inscritos_actuales');

            BitacoraLogger::registrar(
                'POSTULANTE_TURNO_SELECCIONADO',
                'Grupos',
                "Postulante #{$inscripcion->postulante_id} seleccionó turno {$grupo->codigo} ({$grupo->horario?->turno})",
                Auth::id()
            );
        });

        return redirect()->route('grupos.seleccionar', $inscripcion)
            ->with('success', "✅ Te inscribiste correctamente en el turno {$grupo->horario->turno} ({$grupo->codigo}).");
    }
}