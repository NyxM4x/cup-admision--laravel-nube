<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Inscripcion;
use Illuminate\Support\Facades\Auth;

class DashboardPostulanteController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $inscripcion = Inscripcion::whereHas('postulante.persona', function ($q) use ($user) {
                $q->where('ci', $user->ci);
            })
            ->with([
                'postulante.persona',
                'periodo',
                'postulacionCarreras.carrera',
                'pago',
            ])
            ->latest()
            ->first();

        $estadoPago        = $inscripcion?->pago?->estado ?? null;
        $estadoInscripcion = $inscripcion?->estado ?? null;

        $accesoCompleto = in_array($estadoInscripcion, [
            'habilitado',
            'pago_aprobado',
            'pagado',
        ]);

        $grupo = null;
        if ($accesoCompleto && $inscripcion) {
            $grupo = Grupo::whereHas('postulantes', function ($q) use ($inscripcion) {
                    $q->where('postulante_id', $inscripcion->postulante_id);
                })
                ->with([
                    'horario',
                    'aula',
                    'grupoMaterias.materia',
                    'grupoMaterias.docente.persona',
                    'grupoMaterias.aula',
                ])
                ->where('activo', true)
                ->first();
        }

        return view('dashboards.postulante', compact(
            'inscripcion',
            'estadoPago',
            'estadoInscripcion',
            'accesoCompleto',
            'grupo'
        ));
    }
}