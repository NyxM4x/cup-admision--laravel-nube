<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use Illuminate\Http\Request;

class PeriodoController extends Controller
{
    // Listar todos los periodos
    public function index()
    {
        $periodos = Periodo::orderBy('created_at', 'desc')->get();
        return view('periodos.index', compact('periodos'));
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

        Periodo::create([
            'fecha_ini_inscripcion' => $request->fecha_ini_inscripcion,
            'fecha_fin_inscripcion' => $request->fecha_fin_inscripcion,
            'fecha_ini_curso'       => $request->fecha_ini_curso,
            'fecha_fin_curso'       => $request->fecha_fin_curso,
            'activo'                => $request->has('activo'),
        ]);

        return redirect()->route('periodos.index')
            ->with('success', 'Periodo creado correctamente.');
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

        if ($request->has('activo')) {
            Periodo::where('activo', true)->update(['activo' => false]);
        }

        $periodo->update([
            'fecha_ini_inscripcion' => $request->fecha_ini_inscripcion,
            'fecha_fin_inscripcion' => $request->fecha_fin_inscripcion,
            'fecha_ini_curso'       => $request->fecha_ini_curso,
            'fecha_fin_curso'       => $request->fecha_fin_curso,
            'activo'                => $request->has('activo'),
        ]);

        return redirect()->route('periodos.index')
            ->with('success', 'Periodo actualizado correctamente.');
    }

    // Eliminar periodo
    public function destroy(Periodo $periodo)
    {
        $periodo->delete();
        return redirect()->route('periodos.index')
            ->with('success', 'Periodo eliminado correctamente.');
    }
}