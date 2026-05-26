<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    public function index()
    {
        $materias = Materia::orderBy('nombre')->get();
        return view('materias.index', compact('materias'));
    }

    public function create()
    {
        return view('materias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'sigla'         => 'required|string|max:20|unique:materias,sigla',
            'nombre'        => 'required|string|max:100|unique:materias,nombre',
            'dias'          => 'required|in:LMV,MJ',
            'peso_examen1'  => 'required|numeric|min:1|max:98',
            'peso_examen2'  => 'required|numeric|min:1|max:98',
            'peso_examen3'  => 'required|numeric|min:1|max:98',
        ]);

        // Validar que los pesos sumen 100
        $suma = $request->peso_examen1 + $request->peso_examen2 + $request->peso_examen3;
        if (abs($suma - 100) > 0.01) {
            return back()
                ->withErrors(['pesos' => "Los pesos deben sumar 100%. Actualmente suman {$suma}%."])
                ->withInput();
        }

        Materia::create([
            'sigla'        => strtoupper($request->sigla),
            'nombre'       => $request->nombre,
            'dias'         => $request->dias,
            'cant_examenes'=> 3,
            'peso_examen1' => $request->peso_examen1,
            'peso_examen2' => $request->peso_examen2,
            'peso_examen3' => $request->peso_examen3,
            'activo'       => true,
        ]);

        return redirect()->route('materias.index')
            ->with('success', 'Materia registrada correctamente.');
    }

    public function edit(Materia $materia)
    {
        return view('materias.edit', compact('materia'));
    }

    public function update(Request $request, Materia $materia)
    {
        $request->validate([
            'sigla'        => 'required|string|max:20|unique:materias,sigla,' . $materia->id,
            'nombre'       => 'required|string|max:100|unique:materias,nombre,' . $materia->id,
            'dias'         => 'required|in:LMV,MJ',
            'peso_examen1' => 'required|numeric|min:1|max:98',
            'peso_examen2' => 'required|numeric|min:1|max:98',
            'peso_examen3' => 'required|numeric|min:1|max:98',
        ]);

        $suma = $request->peso_examen1 + $request->peso_examen2 + $request->peso_examen3;
        if (abs($suma - 100) > 0.01) {
            return back()
                ->withErrors(['pesos' => "Los pesos deben sumar 100%. Actualmente suman {$suma}%."])
                ->withInput();
        }

        $materia->update([
            'sigla'        => strtoupper($request->sigla),
            'nombre'       => $request->nombre,
            'dias'         => $request->dias,
            'peso_examen1' => $request->peso_examen1,
            'peso_examen2' => $request->peso_examen2,
            'peso_examen3' => $request->peso_examen3,
        ]);

        return redirect()->route('materias.index')
            ->with('success', 'Materia actualizada correctamente.');
    }

    public function destroy(Materia $materia)
    {
        $materia->update(['activo' => false]);
        return redirect()->route('materias.index')
            ->with('success', "Materia '{$materia->nombre}' desactivada.");
    }

    public function reactivar(Materia $materia)
    {
        $materia->update(['activo' => true]);
        return redirect()->route('materias.index')
            ->with('success', "Materia '{$materia->nombre}' reactivada.");
    }
}