<?php

namespace App\Http\Controllers;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Models\Docente;
use App\Models\Materia;
use App\Models\Persona;
use App\Models\Profesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocenteController extends Controller
{
    public function index(Request $request)
    {
        $q      = trim($request->input('q', ''));
        $estado = $request->input('estado', 'activos');

        $query = Docente::with('persona', 'profesion')
            ->orderBy('created_at', 'desc');

        if ($estado === 'activos') {
            $query->where('activo', true);
        } elseif ($estado === 'inactivos') {
            $query->where('activo', false);
        }

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
        $materias    = Materia::where('activo', true)->orderBy('sigla')->get();
        return view('docentes.create', compact('profesiones', 'materias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ci'                  => 'required|string|max:20|unique:personas,ci',
            'nombre'              => 'required|string|max:200',
            'fecha_nacimiento'    => 'nullable|date',
            'sexo'                => 'nullable|in:M,F',
            'direccion'           => 'nullable|string|max:255',
            'telefono'            => 'nullable|string|max:20',
            'correo'              => 'nullable|email|max:150|unique:personas,correo',
            'profesion_id'        => 'nullable|exists:profesiones,id',
            'materias'            => 'required|array|min:1|max:4',
            'materias.*'          => 'exists:materias,sigla',
            'anios_experiencia'   => 'required|integer|min:0|max:50',
            'certif_docente'      => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'certif_profesional'  => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ]);

        $persona = Persona::create([
            'ci'               => $request->ci,
            'nombre'           => $request->nombre,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'sexo'             => $request->sexo,
            'direccion'        => $request->direccion,
            'telefono'         => $request->telefono,
            'correo'           => $request->correo,
        ]);

        $pathCertDocente     = null;
        $pathCertProfesional = null;

        if ($request->hasFile('certif_docente')) {
            $pathCertDocente = $request->file('certif_docente')
                ->store('docentes/certificados', 'public');
        }
        if ($request->hasFile('certif_profesional')) {
            $pathCertProfesional = $request->file('certif_profesional')
                ->store('docentes/certificados', 'public');
        }

        $materias = array_map('strtoupper', $request->materias);

        $docente = Docente::create([
            'persona_id'         => $persona->id,
            'profesion_id'       => $request->profesion_id,
            'materia'            => $materias[0],   // primera materia (compat. GrupoController)
            'anios_experiencia'  => $request->anios_experiencia,
            'certif_docente'     => $pathCertDocente,
            'certif_profesional' => $pathCertProfesional,
            'activo'             => true,
        ]);

        foreach ($materias as $sigla) {
            $docente->docenteMaterias()->firstOrCreate(['materia_sigla' => $sigla]);
        }

        BitacoraLogger::registrar(
            'CREAR', 'Docentes',
            'Docente creado: '.$persona->nombre.' CI='.$persona->ci.' materias='.implode(',', $materias)
        );

        return redirect()->route('docentes.index')
            ->with('success', "Docente '{$persona->nombre}' registrado correctamente.");
    }

    public function edit(Docente $docente)
    {
        $profesiones = Profesion::orderBy('nombre')->get();
        $materias    = Materia::where('activo', true)->orderBy('sigla')->get();
        return view('docentes.edit', compact('docente', 'profesiones', 'materias'));
    }

    public function update(Request $request, Docente $docente)
    {
        $request->validate([
            'ci'                  => 'required|string|max:20|unique:personas,ci,'.$docente->persona_id,
            'nombre'              => 'required|string|max:200',
            'fecha_nacimiento'    => 'nullable|date',
            'sexo'                => 'nullable|in:M,F',
            'direccion'           => 'nullable|string|max:255',
            'telefono'            => 'nullable|string|max:20',
            'correo'              => 'nullable|email|max:150|unique:personas,correo,'.$docente->persona_id,
            'profesion_id'        => 'nullable|exists:profesiones,id',
            'materias'            => 'required|array|min:1|max:4',
            'materias.*'          => 'exists:materias,sigla',
            'anios_experiencia'   => 'required|integer|min:0|max:50',
            'certif_docente'      => 'nullable|file|mimes:pdf,jpg,png|max:5120',
            'certif_profesional'  => 'nullable|file|mimes:pdf,jpg,png|max:5120',
        ]);

        $docente->persona->update([
            'ci'               => $request->ci,
            'nombre'           => $request->nombre,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'sexo'             => $request->sexo,
            'direccion'        => $request->direccion,
            'telefono'         => $request->telefono,
            'correo'           => $request->correo,
        ]);

        if ($request->hasFile('certif_docente')) {
            if ($docente->certif_docente) Storage::disk('public')->delete($docente->certif_docente);
            $docente->certif_docente = $request->file('certif_docente')->store('docentes/certificados', 'public');
        }
        if ($request->hasFile('certif_profesional')) {
            if ($docente->certif_profesional) Storage::disk('public')->delete($docente->certif_profesional);
            $docente->certif_profesional = $request->file('certif_profesional')->store('docentes/certificados', 'public');
        }

        $materias = array_map('strtoupper', $request->materias);

        $docente->update([
            'profesion_id'       => $request->profesion_id,
            'materia'            => $materias[0],   // primera materia (compat. GrupoController)
            'anios_experiencia'  => $request->anios_experiencia,
            'certif_docente'     => $docente->certif_docente,
            'certif_profesional' => $docente->certif_profesional,
        ]);

        // Sincronizar tabla docente_materias
        $docente->docenteMaterias()->whereNotIn('materia_sigla', $materias)->delete();
        foreach ($materias as $sigla) {
            $docente->docenteMaterias()->firstOrCreate(['materia_sigla' => $sigla]);
        }

        BitacoraLogger::registrar(
            'EDITAR', 'Docentes',
            'Docente editado ID='.$docente->id.' materias='.implode(',', $materias)
        );

        return redirect()->route('docentes.index')
            ->with('success', 'Docente actualizado correctamente.');
    }

    public function destroy(Docente $docente)
    {
        $docente->update(['activo' => false]);
        BitacoraLogger::registrar('INACTIVAR', 'Docentes', 'Docente inactivado ID='.$docente->id);
        return back()->with('success', 'Docente inactivado.');
    }
}
