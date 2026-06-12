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

        // Cargar grupos solo si tiene acceso completo
        $grupos = collect();
        if ($accesoCompleto && $inscripcion) {
            $grupos = Grupo::whereHas('postulantes', function ($q) use ($inscripcion) {
                    $q->where('postulante_id', $inscripcion->postulante_id);
                })
                ->with(['materia', 'horario', 'aula', 'docente.persona'])
                ->where('activo', true)
                ->get();
        }

        return view('dashboards.postulante', compact(
            'inscripcion',
            'estadoPago',
            'estadoInscripcion',
            'accesoCompleto',
            'grupos'
        ));
    }
}