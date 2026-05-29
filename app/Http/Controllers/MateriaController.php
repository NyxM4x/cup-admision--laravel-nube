<?php

namespace App\Http\Controllers;

use App\Domain\Bitacora\Services\BitacoraLogger;
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
            'sigla'           => 'required|string|max:20|unique:materias,sigla',
            'nombre'          => 'required|string|max:100|unique:materias,nombre',
            'dias_dictado'    => ['required', 'array', 'min:1'],
            'dias_dictado.*'  => ['in:lunes,martes,miercoles,jueves,viernes,sabado'],
            'hora_inicio'     => ['required', 'date_format:H:i'],
            'hora_fin'        => ['required', 'date_format:H:i', 'after:hora_inicio'],
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
            'dias'         => $this->diasLegacy($request->dias_dictado), // columna legacy NOT NULL
            'dias_dictado' => $request->dias_dictado,
            'hora_inicio'  => $request->hora_inicio,
            'hora_fin'     => $request->hora_fin,
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
            'dias_dictado'    => ['required', 'array', 'min:1'],
            'dias_dictado.*'  => ['in:lunes,martes,miercoles,jueves,viernes,sabado'],
            'hora_inicio'     => ['required', 'date_format:H:i'],
            'hora_fin'        => ['required', 'date_format:H:i', 'after:hora_inicio'],
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
            'dias'         => $this->diasLegacy($request->dias_dictado),
            'dias_dictado' => $request->dias_dictado,
            'hora_inicio'  => $request->hora_inicio,
            'hora_fin'     => $request->hora_fin,
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

    public function destroy(Materia $materia)
    {
        $materia->update(['activo' => false]);

        BitacoraLogger::registrar(
            'DESACTIVAR',
            'Materias',
            'Materia desactivada: '.$materia->nombre.' ID='.$materia->id
        );

        return redirect()->route('materias.index')
            ->with('success', "Materia '{$materia->nombre}' desactivada.");
    }

    public function reactivar(Materia $materia)
    {
        $materia->update(['activo' => true]);

        BitacoraLogger::registrar(
            'ACTIVAR',
            'Materias',
            'Materia reactivada: '.$materia->nombre.' ID='.$materia->id
        );

        return redirect()->route('materias.index')
            ->with('success', "Materia '{$materia->nombre}' reactivada.");
    }

    /**
     * Deriva el código corto legacy para la columna `dias` (NOT NULL) a partir
     * de los días estructurados. Ej: [lunes, miercoles, viernes] => "LXV".
     */
    private function diasLegacy(array $dias): string
    {
        $map = ['lunes' => 'L', 'martes' => 'M', 'miercoles' => 'X', 'jueves' => 'J', 'viernes' => 'V', 'sabado' => 'S'];

        return collect($dias)->map(fn ($d) => $map[$d] ?? '')->join('') ?: '-';
    }
}