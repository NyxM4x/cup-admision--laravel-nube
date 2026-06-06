<?php

namespace App\Services;

use App\Models\CupoCarrera;
use App\Models\Periodo;
use App\Models\PostulacionCarrera;
use App\Models\ResultadoAdmision;
use Illuminate\Support\Facades\DB;

class AsignacionCarreraService
{
    /**
     * Asigna carrera definitiva por mérito (promedio DESC) y cupo en cascada:
     * 1ra preferencia → 2da preferencia → sin cupo.
     */
    public function ejecutarAsignacion(Periodo $periodo): array
    {
        $aprobados = ResultadoAdmision::with('postulante.inscripciones')
            ->where('periodo_id', $periodo->id)
            ->where('estado_admision', 'aprobado')
            ->orderByDesc('promedio_final')
            ->get();

        // Cupos del periodo (carrera_id => cupo_max)
        $cupos = CupoCarrera::where('periodo_id', $periodo->id)
            ->pluck('cupo_max', 'carrera_id')->toArray();

        $contadorPorCarrera = [];
        $stats = [
            'total_aprobados'   => $aprobados->count(),
            'admitidos_primera' => 0,
            'admitidos_segunda' => 0,
            'sin_cupo'          => 0,
            'lista_espera'      => 0,
        ];

        DB::transaction(function () use ($aprobados, $periodo, $cupos, &$contadorPorCarrera, &$stats) {
            $posicion = 1;

            foreach ($aprobados as $resultado) {
                $resultado->posicion_ranking_general = $posicion++;

                // Inscripción del periodo → preferencias de carrera
                $inscripcion = $resultado->postulante->inscripciones
                    ->firstWhere('periodo_id', $periodo->id)
                    ?? $resultado->postulante->inscripciones->first();

                $preferencias = $inscripcion
                    ? PostulacionCarrera::where('inscripcion_id', $inscripcion->id)
                        ->orderBy('prioridad')->get()
                    : collect();

                $asignada = false;
                foreach ($preferencias as $pref) {
                    if (! isset($cupos[$pref->carrera_id])) {
                        continue;
                    }
                    $usado = $contadorPorCarrera[$pref->carrera_id] ?? 0;
                    if ($usado < $cupos[$pref->carrera_id]) {
                        $resultado->carrera_asignada_id = $pref->carrera_id;
                        $resultado->estado_admision = $pref->prioridad == 1
                            ? 'admitido_primera' : 'admitido_segunda';
                        $resultado->fecha_asignacion = now();
                        $contadorPorCarrera[$pref->carrera_id] = $usado + 1;
                        $stats[$pref->prioridad == 1 ? 'admitidos_primera' : 'admitidos_segunda']++;
                        $asignada = true;
                        break;
                    }
                }

                if (! $asignada) {
                    $resultado->estado_admision = 'no_admitido_sin_cupo';
                    $resultado->observacion = 'Aprobó pero no había cupo en sus preferencias.';
                    $stats['sin_cupo']++;
                }

                $resultado->save();

                // Reflejar en el postulante
                $resultado->postulante->update([
                    'estado' => $asignada ? 'aprobado' : 'reprobado',
                ]);
            }
        });

        return $stats;
    }
}
