<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\Bitacora;
use App\Models\Pago;
use App\Models\Postulante;
use App\Models\Rol;
use App\Models\User;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $totalUsuarios    = User::count();
        $totalRoles       = Rol::where('activo', true)->count();
        $totalAulas       = Aula::where('activo', true)->count();
        $totalBitacora    = Bitacora::count();
        $totalPostulantes = Postulante::count();
        $totalPagos       = Pago::where('estado', 'pendiente')->count();

        return view('dashboards.admin', compact(
            'totalUsuarios',
            'totalRoles',
            'totalAulas',
            'totalBitacora',
            'totalPostulantes',
            'totalPagos'
        ));
    }
}