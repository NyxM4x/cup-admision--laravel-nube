<?php

namespace App\Http\Controllers;

use App\Domain\Bitacora\Services\BitacoraLogger;
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

        try {
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

            BitacoraLogger::registrar(
                'CREAR',
                'Carreras',
                'Carrera creada: '.$carrera->nombre.' (codigo: '.$carrera->codigo.')'
            );

            return redirect()->route('carreras.index')
                ->with('success', "Carrera '{$carrera->nombre}' registrada correctamente.");
        } catch (\Throwable $e) {
            BitacoraLogger::registrar(
                'ERROR_CREAR',
                'Carreras',
                'Error al crear carrera: '.$e->getMessage()
            );

            throw $e;
        }
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

        try {
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

            BitacoraLogger::registrar(
                'EDITAR',
                'Carreras',
                'Carrera editada ID='.$carrera->id.' nombre='.$carrera->nombre
            );

            return redirect()->route('carreras.index')
                ->with('success', "Carrera '{$carrera->nombre}' actualizada correctamente.");
        } catch (\Throwable $e) {
            BitacoraLogger::registrar(
                'ERROR_EDITAR',
                'Carreras',
                'Error al editar carrera ID='.$carrera->id.': '.$e->getMessage()
            );

            throw $e;
        }
    }

    public function destroy(Carrera $carrera)
    {
        try {
            // Regla CU08: No se puede inactivar con postulantes asociados al periodo activo
            // Por ahora hacemos soft-delete lógico (inactivar, no borrar)
            $carrera->update(['activo' => false]);

            BitacoraLogger::registrar(
                'DESACTIVAR',
                'Carreras',
                'Carrera desactivada: '.$carrera->nombre.' ID='.$carrera->id
            );

            return redirect()->route('carreras.index')
                ->with('success', "Carrera '{$carrera->nombre}' desactivada correctamente.");
        } catch (\Throwable $e) {
            BitacoraLogger::registrar(
                'ERROR_DESACTIVAR',
                'Carreras',
                'Error al desactivar carrera ID='.$carrera->id.': '.$e->getMessage()
            );

            throw $e;
        }
    }

    public function reactivar(Carrera $carrera)
    {
        try {
            $carrera->update(['activo' => true]);

            BitacoraLogger::registrar(
                'ACTIVAR',
                'Carreras',
                'Carrera reactivada: '.$carrera->nombre.' ID='.$carrera->id
            );

            return redirect()->route('carreras.index')
                ->with('success', "Carrera '{$carrera->nombre}' reactivada correctamente.");
        } catch (\Throwable $e) {
            BitacoraLogger::registrar(
                'ERROR_ACTIVAR',
                'Carreras',
                'Error al reactivar carrera ID='.$carrera->id.': '.$e->getMessage()
            );

            throw $e;
        }
    }
}