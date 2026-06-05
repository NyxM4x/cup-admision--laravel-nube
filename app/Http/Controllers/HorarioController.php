<?php

namespace App\Http\Controllers;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Models\Horario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HorarioController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->input('q', ''));
        $estado = $request->input('estado', 'todos'); // todos|activos|inactivos

        $query = Horario::orderBy('hora_inicio');

        if ($estado === 'activos') {
            $query->where('activo', true);
        } elseif ($estado === 'inactivos') {
            $query->where('activo', false);
        }

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->whereRaw('unaccent(codigo) ilike unaccent(?)', ["%{$q}%"])
                  ->orWhereRaw('unaccent(turno) ilike unaccent(?)', ["%{$q}%"])
                  ->orWhereRaw('unaccent(dias) ilike unaccent(?)', ["%{$q}%"]);
            });
        }

        $horarios = $query->paginate(20)->withQueryString();

        return view('horarios.index', compact('horarios', 'q', 'estado'));
    }

    public function create()
    {
        return view('horarios.create');
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);

        $horario = Horario::create($data + ['activo' => true]);

        BitacoraLogger::registrar(
            'HORARIO_CREADO',
            'Horarios',
            "Horario {$horario->codigo} ({$horario->turno} {$horario->rango}) creado",
            Auth::id()
        );

        return redirect()->route('horarios.index')
            ->with('success', "Horario '{$horario->codigo}' creado correctamente.");
    }

    public function edit(Horario $horario)
    {
        return view('horarios.edit', compact('horario'));
    }

    public function update(Request $request, Horario $horario)
    {
        $data = $this->validar($request, $horario->id);

        $horario->update($data);

        BitacoraLogger::registrar(
            'HORARIO_EDITADO',
            'Horarios',
            "Horario {$horario->codigo} editado",
            Auth::id()
        );

        return redirect()->route('horarios.index')
            ->with('success', "Horario '{$horario->codigo}' actualizado correctamente.");
    }

    public function archivar(Horario $horario)
    {
        $horario->update(['activo' => false]);

        BitacoraLogger::registrar(
            'HORARIO_ARCHIVADO',
            'Horarios',
            "Horario {$horario->codigo} archivado",
            Auth::id()
        );

        return back()->with('success', "Horario '{$horario->codigo}' archivado.");
    }

    public function reactivar(Horario $horario)
    {
        $horario->update(['activo' => true]);

        BitacoraLogger::registrar(
            'HORARIO_REACTIVADO',
            'Horarios',
            "Horario {$horario->codigo} reactivado",
            Auth::id()
        );

        return back()->with('success', "Horario '{$horario->codigo}' reactivado.");
    }

    private function validar(Request $request, ?int $ignoreId = null): array
    {
        $unique = 'unique:horarios,codigo'.($ignoreId ? ','.$ignoreId : '');

        return $request->validate([
            'codigo'      => "required|string|max:20|{$unique}",
            'turno'       => 'required|in:Mañana,Tarde,Noche',
            'dias'        => 'required|string|max:50',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin'    => 'required|date_format:H:i|after:hora_inicio',
            'descripcion' => 'nullable|string|max:200',
        ], [
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        ]);
    }
}
