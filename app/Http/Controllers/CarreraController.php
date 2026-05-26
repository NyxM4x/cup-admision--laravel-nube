<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\CupoCarrera;
use App\Models\Periodo;
use Illuminate\Http\Request;

class CarreraController extends Controller
{
    public function index()
    {
        $periodoActivo = Periodo::where('activo', true)->first();

        $carreras = Carrera::with(['cupoActivo'])->orderBy('nombre')->get();

        return view('carreras.index', compact('carreras', 'periodoActivo'));
    }

    public function create()
    {
        $periodoActivo = Periodo::where('activo', true)->first();
        return view('carreras.create', compact('periodoActivo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo'      => 'required|string|max:20|unique:carreras,codigo',
            'nombre'      => 'required|string|max:150|unique:carreras,nombre',
            'descripcion' => 'nullable|string',
            'cupo_max'    => 'required|integer|min:1',
        ]);

        $periodoActivo = Periodo::where('activo', true)->first();

        if (!$periodoActivo) {
            return back()->withErrors(['general' => 'No existe un periodo activo. Crea un periodo antes de registrar carreras.'])->withInput();
        }

        $carrera = Carrera::create([
            'codigo'      => strtoupper($request->codigo),
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo'      => true,
        ]);

        // Crear el cupo asociado al periodo activo
        CupoCarrera::create([
            'carrera_id' => $carrera->id,
            'periodo_id' => $periodoActivo->id,
            'cupo_max'   => $request->cupo_max,
            'fecha_cofi' => $request->fecha_cofi ?? null,
        ]);

        return redirect()->route('carreras.index')
            ->with('success', "Carrera '{$carrera->nombre}' registrada correctamente.");
    }

    public function edit(Carrera $carrera)
    {
        $periodoActivo = Periodo::where('activo', true)->first();

        // Cupo del periodo activo para esta carrera
        $cupoActual = null;
        if ($periodoActivo) {
            $cupoActual = CupoCarrera::where('carrera_id', $carrera->id)
                ->where('periodo_id', $periodoActivo->id)
                ->first();
        }

        return view('carreras.edit', compact('carrera', 'periodoActivo', 'cupoActual'));
    }

    public function update(Request $request, Carrera $carrera)
    {
        $request->validate([
            'codigo'      => 'required|string|max:20|unique:carreras,codigo,' . $carrera->id,
            'nombre'      => 'required|string|max:150|unique:carreras,nombre,' . $carrera->id,
            'descripcion' => 'nullable|string',
            'cupo_max'    => 'required|integer|min:1',
        ]);

        $periodoActivo = Periodo::where('activo', true)->first();

        $carrera->update([
            'codigo'      => strtoupper($request->codigo),
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        // Actualizar o crear el cupo del periodo activo
        if ($periodoActivo) {
            CupoCarrera::updateOrCreate(
                ['carrera_id' => $carrera->id, 'periodo_id' => $periodoActivo->id],
                ['cupo_max'   => $request->cupo_max, 'fecha_cofi' => $request->fecha_cofi ?? null]
            );
        }

        return redirect()->route('carreras.index')
            ->with('success', "Carrera '{$carrera->nombre}' actualizada correctamente.");
    }

    public function destroy(Carrera $carrera)
    {
        // Regla CU08: No se puede inactivar con postulantes asociados al periodo activo
        // Por ahora hacemos soft-delete lógico (inactivar, no borrar)
        $carrera->update(['activo' => false]);

        return redirect()->route('carreras.index')
            ->with('success', "Carrera '{$carrera->nombre}' desactivada correctamente.");
    }

    public function reactivar(Carrera $carrera)
{
    $carrera->update(['activo' => true]);
    return redirect()->route('carreras.index')
        ->with('success', "Carrera '{$carrera->nombre}' reactivada correctamente.");
}
}