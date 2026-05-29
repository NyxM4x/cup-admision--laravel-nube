<?php

namespace App\Http\Controllers;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Models\Docente;
use App\Models\Persona;
use App\Models\Profesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocenteController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->input('q', ''));
        $estado = $request->input('estado', 'activos'); // activos|inactivos|todos

        $query = Docente::with('persona', 'profesion')
            ->orderBy('created_at', 'desc');

        // Filtro de estado lógico
        if ($estado === 'activos') {
            $query->where('activo', true);
        } elseif ($estado === 'inactivos') {
            $query->where('activo', false);
        }

        // Buscador por nombre, CI o profesión
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->whereHas('persona', function ($p) use ($q) {
                    $p->whereRaw('unaccent(nombre) ilike unaccent(?)', ["%{$q}%"])
                      ->orWhere('ci', 'ilike', "%{$q}%");
                })->orWhereHas('profesion', function ($p) use ($q) {
                    $p->whereRaw('unaccent(nombre) ilike unaccent(?)', ["%{$q}%"]);
                });
            });
        }

        $docentes = $query->get();

        return view('docentes.index', compact('docentes', 'q', 'estado'));
    }

    public function create()
    {
        $profesiones = Profesion::orderBy('nombre')->get();
        return view('docentes.create', compact('profesiones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // Datos Persona
            'ci'                  => 'required|string|max:20|unique:personas,ci',
            'nombre'              => 'required|string|max:200',
            'fecha_nacimiento'    => 'nullable|date',
            'sexo'                => 'nullable|in:M,F',
            'direccion'           => 'nullable|string|max:255',
            'telefono'            => 'nullable|string|max:20',
            'correo'              => 'nullable|email|max:150|unique:personas,correo',
            // Datos Docente
            'profesion_id'        => 'nullable|exists:profesiones,id',
            'anios_experiencia'   => 'required|integer|min:0|max:50',
            'certif_docente'      => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'certif_profesional'  => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ]);

        // 1. Crear Persona
        $persona = Persona::create([
            'ci'               => $request->ci,
            'nombre'           => $request->nombre,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'sexo'             => $request->sexo,
            'direccion'        => $request->direccion,
            'telefono'         => $request->telefono,
            'correo'           => $request->correo,
        ]);

        // 2. Subir certificados si existen
        $pathCertDocente      = null;
        $pathCertProfesional  = null;

        if ($request->hasFile('certif_docente')) {
            $pathCertDocente = $request->file('certif_docente')
                ->store('docentes/certificados', 'public');
        }

        if ($request->hasFile('certif_profesional')) {
            $pathCertProfesional = $request->file('certif_profesional')
                ->store('docentes/certificados', 'public');
        }

        // 3. Crear Docente
        $docente = Docente::create([
            'persona_id'         => $persona->id,
            'profesion_id'       => $request->profesion_id,
            'anios_experiencia'  => $request->anios_experiencia,
            'certif_docente'     => $pathCertDocente,
            'certif_profesional' => $pathCertProfesional,
            'activo'             => true,
        ]);

        BitacoraLogger::registrar(
            'CREAR',
            'Docentes',
            'Docente creado: '.$persona->nombre.' CI='.$persona->ci.' ID='.$docente->id
        );

        return redirect()->route('docentes.index')
            ->with('success', "Docente '{$persona->nombre}' registrado correctamente.");
    }

    public function edit(Docente $docente)
    {
        $profesiones = Profesion::orderBy('nombre')->get();
        return view('docentes.edit', compact('docente', 'profesiones'));
    }

    public function update(Request $request, Docente $docente)
    {
        $request->validate([
            'ci'                 => 'required|string|max:20|unique:personas,ci,' . $docente->persona_id,
            'nombre'             => 'required|string|max:200',
            'fecha_nacimiento'   => 'nullable|date',
            'sexo'               => 'nullable|in:M,F',
            'direccion'          => 'nullable|string|max:255',
            'telefono'           => 'nullable|string|max:20',
            'correo'             => 'nullable|email|max:150|unique:personas,correo,' . $docente->persona_id,
            'profesion_id'       => 'nullable|exists:profesiones,id',
            'anios_experiencia'  => 'required|integer|min:0|max:50',
            'certif_docente'     => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'certif_profesional' => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ]);

        // Actualizar Persona
        $docente->persona->update([
            'ci'               => $request->ci,
            'nombre'           => $request->nombre,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'sexo'             => $request->sexo,
            'direccion'        => $request->direccion,
            'telefono'         => $request->telefono,
            'correo'           => $request->correo,
        ]);

        // Actualizar certificados si se subieron nuevos
        $pathCertDocente     = $docente->certif_docente;
        $pathCertProfesional = $docente->certif_profesional;

        if ($request->hasFile('certif_docente')) {
            if ($pathCertDocente) Storage::disk('public')->delete($pathCertDocente);
            $pathCertDocente = $request->file('certif_docente')
                ->store('docentes/certificados', 'public');
        }

        if ($request->hasFile('certif_profesional')) {
            if ($pathCertProfesional) Storage::disk('public')->delete($pathCertProfesional);
            $pathCertProfesional = $request->file('certif_profesional')
                ->store('docentes/certificados', 'public');
        }

        // Actualizar Docente
        $docente->update([
            'profesion_id'       => $request->profesion_id,
            'anios_experiencia'  => $request->anios_experiencia,
            'certif_docente'     => $pathCertDocente,
            'certif_profesional' => $pathCertProfesional,
        ]);

        BitacoraLogger::registrar(
            'EDITAR',
            'Docentes',
            'Docente editado: '.$docente->persona->nombre.' ID='.$docente->id
        );

        return redirect()->route('docentes.index')
            ->with('success', "Docente '{$docente->persona->nombre}' actualizado correctamente.");
    }

    public function destroy(Docente $docente)
    {
        $docente->update(['activo' => false]);

        BitacoraLogger::registrar(
            'DESACTIVAR',
            'Docentes',
            'Docente desactivado: '.$docente->persona->nombre.' ID='.$docente->id
        );

        return redirect()->route('docentes.index')
            ->with('success', "Docente '{$docente->persona->nombre}' desactivado.");
    }

    public function reactivar(Docente $docente)
    {
        $docente->update(['activo' => true]);

        BitacoraLogger::registrar(
            'ACTIVAR',
            'Docentes',
            'Docente reactivado: '.$docente->persona->nombre.' ID='.$docente->id
        );

        return redirect()->route('docentes.index')
            ->with('success', "Docente '{$docente->persona->nombre}' reactivado.");
    }
}