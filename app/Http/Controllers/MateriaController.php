<?php

namespace App\Http\Controllers;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Models\Materia;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->input('q', ''));
        $estado = $request->input('estado', 'todos'); // todos|activos|inactivos

        $query = Materia::orderBy('sigla');

        if ($estado === 'activos') {
            $query->where('activo', true);
        } elseif ($estado === 'inactivos') {
            $query->where('activo', false);
        }

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->whereRaw('unaccent(sigla) ilike unaccent(?)', ["%{$q}%"])
                  ->orWhereRaw('unaccent(nombre) ilike unaccent(?)', ["%{$q}%"]);
            });
        }

        $materias = $query->paginate(20)->withQueryString();

        return view('materias.index', compact('materias', 'q', 'estado'));
    }

    public function create()
    {
        return view('materias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'sigla'           => 'required|string|max:20|unique:materias,sigla',
            'nombre'          => 'required|string|max:100|unique:materias,nombre',
            'peso_examen1'    => 'required|numeric|min:1|max:98',
            'peso_examen2'    => 'required|numeric|min:1|max:98',
            'peso_examen3'    => 'required|numeric|min:1|max:98',
        ]);

        // Validar que los pesos sumen 100
        $suma = $request->peso_examen1 + $request->peso_examen2 + $request->peso_examen3;
        if (abs($suma - 100) > 0.01) {
            return back()
                ->withErrors(['pesos' => "Los pesos deben sumar 100%. Actualmente suman {$suma}%."])
                ->withInput();
        }

        $materia = Materia::create([
            'sigla'        => strtoupper($request->sigla),
            'nombre'       => $request->nombre,
            'dias'         => 'LMV', // legacy NOT NULL; el horario real ira en el grupo (post 09/06)
            'cant_examenes'=> 3,
            'peso_examen1' => $request->peso_examen1,
            'peso_examen2' => $request->peso_examen2,
            'peso_examen3' => $request->peso_examen3,
            'activo'       => true,
        ]);

        BitacoraLogger::registrar(
            'CREAR',
            'Materias',
            'Materia creada: '.$materia->nombre.' (sigla: '.$materia->sigla.')'
        );

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
            'sigla'           => 'required|string|max:20|unique:materias,sigla,' . $materia->id,
            'nombre'          => 'required|string|max:100|unique:materias,nombre,' . $materia->id,
            'peso_examen1'    => 'required|numeric|min:1|max:98',
            'peso_examen2'    => 'required|numeric|min:1|max:98',
            'peso_examen3'    => 'required|numeric|min:1|max:98',
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
            'peso_examen1' => $request->peso_examen1,
            'peso_examen2' => $request->peso_examen2,
            'peso_examen3' => $request->peso_examen3,
        ]);

        BitacoraLogger::registrar(
            'EDITAR',
            'Materias',
            'Materia editada ID='.$materia->id.' nombre='.$materia->nombre
        );

        return redirect()->route('materias.index')
            ->with('success', 'Materia actualizada correctamente.');
    }

    // Archivar materia (inactivación lógica — NO se elimina físicamente)
    public function archivar(Materia $materia)
    {
        $materia->update(['activo' => false]);

        BitacoraLogger::registrar(
            'MATERIA_ARCHIVADA',
            'Materias',
            'Materia archivada: '.$materia->nombre.' (sigla: '.$materia->sigla.')'
        );

        return redirect()->route('materias.index')
            ->with('success', "Materia '{$materia->nombre}' archivada.");
    }

    public function reactivar(Materia $materia)
    {
        $materia->update(['activo' => true]);

        BitacoraLogger::registrar(
            'MATERIA_REACTIVADA',
            'Materias',
            'Materia reactivada: '.$materia->nombre.' (sigla: '.$materia->sigla.')'
        );

        return redirect()->route('materias.index')
            ->with('success', "Materia '{$materia->nombre}' reactivada.");
    }
}