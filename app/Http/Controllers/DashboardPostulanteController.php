<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use Illuminate\Support\Facades\Auth;

class DashboardPostulanteController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Buscar la inscripción activa del postulante por CI
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

        $estadoPago   = $inscripcion?->pago?->estado ?? null;
        $estadoInscripcion = $inscripcion?->estado ?? null;

        // Determinar qué ve el postulante
        $accesoCompleto = in_array($estadoInscripcion, ['habilitado', 'pago_aprobado']);

        return view('dashboards.postulante', compact(
            'inscripcion',
            'estadoPago',
            'estadoInscripcion',
            'accesoCompleto'
        ));
    }
}