<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermisoController extends Controller
{
    /**
     * Catálogo (read-only) de permisos agrupados por módulo.
     */
    public function index(Request $request): View
    {
        $q = $request->input('q');
        $modulo = $request->input('modulo');

        $query = Permiso::query();

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('codigo', 'ilike', "%{$q}%")
                    ->orWhere('descripcion', 'ilike', "%{$q}%");
            });
        }

        if ($modulo) {
            $query->where('modulo', $modulo);
        }

        $permisos = $query->withCount('roles')->orderBy('modulo')->orderBy('codigo')->get()->groupBy('modulo');
        $modulosDisponibles = Permiso::distinct()->orderBy('modulo')->pluck('modulo');

        return view('seguridad.permisos.index', compact('permisos', 'modulosDisponibles', 'q', 'modulo'));
    }

    /**
     * Matriz de auditoría rol x permiso.
     */
    public function matriz(): View
    {
        $roles = Rol::with('permisos')->where('activo', true)->orderBy('nombre')->get();
        $permisos = Permiso::orderBy('modulo')->orderBy('codigo')->get();

        return view('seguridad.permisos.matriz', compact('roles', 'permisos'));
    }
}
