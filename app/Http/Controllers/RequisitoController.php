<?php

namespace App\Http\Controllers;

use App\Models\Requisito;
use App\Models\Periodo;
use Illuminate\Http\Request;

class RequisitoController extends Controller
{
    public function index()
    {
        $periodoActivo = Periodo::where('activo', true)->first();
        $requisitos = collect();

        if ($periodoActivo) {
            $requisitos = Requisito::where('periodo_id', $periodoActivo->id)
                ->orderBy('obligatorio', 'desc')
                ->orderBy('nombre')
                ->get();
        }

        return view('requisitos.index', compact('requisitos', 'periodoActivo'));
    }

    public function create()
    {
        $periodoActivo = Periodo::where('activo', true)->first();
        return view('requisitos.create', compact('periodoActivo'));
    }

    public function store(Request $request)
    {
        $periodoActivo = Periodo::where('activo', true)->first();

        if (!$periodoActivo) {
            return back()->withErrors(['general' => 'No existe un periodo activo.'])->withInput();
        }

        $request->validate([
            'nombre'           => 'required|string|max:150',
            'descripcion'      => 'nullable|string',
            'obligatorio'      => 'nullable|boolean',
            'formato_aceptado' => 'required|string',
            'tamanio_max_kb'   => 'required|integer|min:100|max:20480',
        ]);

        // Verificar nombre duplicado en el mismo periodo
        $existe = Requisito::where('periodo_id', $periodoActivo->id)
            ->where('nombre', $request->nombre)
            ->exists();

        if ($existe) {
            return back()->withErrors(['nombre' => 'Ya existe un requisito con ese nombre en el periodo activo.'])->withInput();
        }

        Requisito::create([
            'periodo_id'       => $periodoActivo->id,
            'nombre'           => $request->nombre,
            'descripcion'      => $request->descripcion,
            'obligatorio'      => $request->has('obligatorio'),
            'formato_aceptado' => $request->formato_aceptado,
            'tamanio_max_kb'   => $request->tamanio_max_kb,
            'activo'           => true,
        ]);

        return redirect()->route('requisitos.index')
            ->with('success', 'Requisito registrado correctamente.');
    }

    public function edit(Requisito $requisito)
    {
        return view('requisitos.edit', compact('requisito'));
    }

    public function update(Request $request, Requisito $requisito)
    {
        $request->validate([
            'nombre'           => 'required|string|max:150',
            'descripcion'      => 'nullable|string',
            'obligatorio'      => 'nullable|boolean',
            'formato_aceptado' => 'required|string',
            'tamanio_max_kb'   => 'required|integer|min:100|max:20480',
        ]);

        $requisito->update([
            'nombre'           => $request->nombre,
            'descripcion'      => $request->descripcion,
            'obligatorio'      => $request->has('obligatorio'),
            'formato_aceptado' => $request->formato_aceptado,
            'tamanio_max_kb'   => $request->tamanio_max_kb,
        ]);

        return redirect()->route('requisitos.index')
            ->with('success', 'Requisito actualizado correctamente.');
    }

    public function destroy(Requisito $requisito)
    {
        // Regla CU11: No se puede inactivar requisito obligatorio con docs pendientes
        // Por ahora inactivamos lógicamente
        $requisito->update(['activo' => false]);
        return redirect()->route('requisitos.index')
            ->with('success', "Requisito '{$requisito->nombre}' desactivado.");
    }

    public function reactivar(Requisito $requisito)
    {
        $requisito->update(['activo' => true]);
        return redirect()->route('requisitos.index')
            ->with('success', "Requisito '{$requisito->nombre}' reactivado.");
    }
}