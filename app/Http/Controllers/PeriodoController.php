<?php

namespace App\Http\Controllers;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeriodoController extends Controller
{
    // Listar todos los periodos
    public function index(Request $request)
    {
        $q = trim($request->input('q', ''));
        $estado = $request->input('estado', 'todos'); // todos|activos|inactivos

        $query = Periodo::orderBy('created_at', 'desc');

        if ($estado === 'activos') {
            $query->where('activo', true);
        } elseif ($estado === 'inactivos') {
            $query->where('activo', false);
        }

        // Periodos no tiene codigo/nombre: se busca por año o fecha (dd/mm/aaaa)
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->whereRaw("to_char(fecha_ini_inscripcion, 'DD/MM/YYYY') ilike ?", ["%{$q}%"])
                  ->orWhereRaw("to_char(fecha_fin_curso, 'DD/MM/YYYY') ilike ?", ["%{$q}%"])
                  ->orWhereRaw("to_char(fecha_ini_inscripcion, 'YYYY') ilike ?", ["%{$q}%"]);
            });
        }

        $periodos = $query->paginate(20)->withQueryString();

        return view('periodos.index', compact('periodos', 'q', 'estado'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('periodos.create');
    }

    // Guardar nuevo periodo
    public function store(Request $request)
    {
        $request->validate([
            'fecha_ini_inscripcion' => 'required|date',
            'fecha_fin_inscripcion' => 'required|date|after:fecha_ini_inscripcion',
            'fecha_ini_curso'       => 'required|date|after:fecha_fin_inscripcion',
            'fecha_fin_curso'       => 'required|date|after:fecha_ini_curso',
        ]);

        // Solo puede haber un periodo activo
        if ($request->has('activo')) {
            Periodo::where('activo', true)->update(['activo' => false]);
        }

        try {
            $periodo = Periodo::create([
                'fecha_ini_inscripcion' => $request->fecha_ini_inscripcion,
                'fecha_fin_inscripcion' => $request->fecha_fin_inscripcion,
                'fecha_ini_curso'       => $request->fecha_ini_curso,
                'fecha_fin_curso'       => $request->fecha_fin_curso,
                'activo'                => $request->has('activo'),
            ]);

            BitacoraLogger::registrar(
                'CREAR',
                'PeriodoAcademico',
                'Periodo creado ID='.$periodo->id.' fechas: '.$periodo->fecha_ini_inscripcion.' - '.$periodo->fecha_fin_curso
            );

            return redirect()->route('periodos.index')
                ->with('success', 'Periodo creado correctamente.');
        } catch (\Throwable $e) {
            BitacoraLogger::registrar(
                'ERROR_CREAR',
                'PeriodoAcademico',
                'Error al crear periodo: '.$e->getMessage()
            );

            throw $e;
        }
    }

    // Mostrar formulario de edición
    public function edit(Periodo $periodo)
    {
        return view('periodos.edit', compact('periodo'));
    }

    // Actualizar periodo
    public function update(Request $request, Periodo $periodo)
    {
        $request->validate([
            'fecha_ini_inscripcion' => 'required|date',
            'fecha_fin_inscripcion' => 'required|date|after:fecha_ini_inscripcion',
            'fecha_ini_curso'       => 'required|date|after:fecha_fin_inscripcion',
            'fecha_fin_curso'       => 'required|date|after:fecha_ini_curso',
        ]);

        try {
            $periodoAnteriorActivo = $periodo->activo;

            if ($request->has('activo')) {
                Periodo::where('activo', true)->where('id', '!=', $periodo->id)->update(['activo' => false]);
            }

            $periodo->update([
                'fecha_ini_inscripcion' => $request->fecha_ini_inscripcion,
                'fecha_fin_inscripcion' => $request->fecha_fin_inscripcion,
                'fecha_ini_curso'       => $request->fecha_ini_curso,
                'fecha_fin_curso'       => $request->fecha_fin_curso,
                'activo'                => $request->has('activo'),
            ]);

            BitacoraLogger::registrar(
                'EDITAR',
                'PeriodoAcademico',
                'Periodo editado ID='.$periodo->id.' fechas: '.$periodo->fecha_ini_inscripcion.' - '.$periodo->fecha_fin_curso
            );

            if (! $periodoAnteriorActivo && $periodo->activo) {
                BitacoraLogger::registrar(
                    'ACTIVAR',
                    'PeriodoAcademico',
                    'Periodo activado ID='.$periodo->id
                );
            }

            if ($periodoAnteriorActivo && ! $periodo->activo) {
                BitacoraLogger::registrar(
                    'DESACTIVAR',
                    'PeriodoAcademico',
                    'Periodo desactivado ID='.$periodo->id
                );
            }

            return redirect()->route('periodos.index')
                ->with('success', 'Periodo actualizado correctamente.');
        } catch (\Throwable $e) {
            BitacoraLogger::registrar(
                'ERROR_EDITAR',
                'PeriodoAcademico',
                'Error al editar periodo ID='.$periodo->id.': '.$e->getMessage()
            );

            throw $e;
        }
    }

    // Archivar periodo (inactivación lógica — NO se elimina físicamente)
    public function archivar(Periodo $periodo)
    {
        $periodo->update(['activo' => false]);

        BitacoraLogger::registrar(
            'PERIODO_ARCHIVADO',
            'PeriodoAcademico',
            'Periodo #'.$periodo->id.' archivado ('
                .$periodo->fecha_ini_inscripcion->format('d/m/Y').' - '
                .$periodo->fecha_fin_curso->format('d/m/Y').')',
            Auth::id()
        );

        return back()->with('success', 'Periodo archivado correctamente.');
    }

    public function reactivar(Periodo $periodo)
    {
        // Solo puede haber un periodo activo: desactivar los demás al reactivar
        Periodo::where('activo', true)->where('id', '!=', $periodo->id)->update(['activo' => false]);
        $periodo->update(['activo' => true]);

        BitacoraLogger::registrar(
            'PERIODO_REACTIVADO',
            'PeriodoAcademico',
            'Periodo #'.$periodo->id.' reactivado',
            Auth::id()
        );

        return back()->with('success', 'Periodo reactivado correctamente.');
    }
}