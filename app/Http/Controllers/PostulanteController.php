<?php

namespace App\Http\Controllers;

use App\Models\Postulante;
use App\Models\Persona;
use App\Models\Inscripcion;
use App\Models\PostulacionCarrera;
use App\Models\Carrera;
use App\Models\Periodo;
use Illuminate\Http\Request;

class PostulanteController extends Controller
{
    public function index()
    {
        $periodoActivo = Periodo::where('activo', true)->first();

        // Traer postulantes con su inscripción del periodo activo
        $postulantes = Postulante::with([
                'persona',
                'inscripciones' => function($q) use ($periodoActivo) {
                    if ($periodoActivo) {
                        $q->where('periodo_id', $periodoActivo->id)
                          ->with('postulacionCarreras.carrera');
                    }
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('postulantes.index', compact('postulantes', 'periodoActivo'));
    }

    public function create()
    {
        $periodoActivo = Periodo::where('activo', true)->first();
        $carreras      = Carrera::where('activo', true)->orderBy('nombre')->get();

        return view('postulantes.create', compact('periodoActivo', 'carreras'));
    }

    public function store(Request $request)
    {
        $periodoActivo = Periodo::where('activo', true)->first();

        if (!$periodoActivo) {
            return back()->withErrors(['general' => 'No existe un periodo activo.'])->withInput();
        }

        $request->validate([
            // Datos Persona
            'ci'               => 'required|string|max:20|unique:personas,ci',
            'nombre'           => 'required|string|max:200',
            'fecha_nacimiento' => 'nullable|date',
            'sexo'             => 'nullable|in:M,F',
            'direccion'        => 'nullable|string|max:255',
            'telefono'         => 'nullable|string|max:20',
            'correo'           => 'nullable|email|max:150|unique:personas,correo',
            // Datos Postulante
            'colegio'          => 'required|string|max:200',
            // Carreras
            'carrera_1'        => 'required|exists:carreras,id',
            'carrera_2'        => 'nullable|exists:carreras,id|different:carrera_1',
        ], [
            'carrera_1.required'  => 'Debe seleccionar al menos una carrera (primera opción).',
            'carrera_2.different' => 'La segunda carrera debe ser diferente a la primera.',
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

        // 2. Crear Postulante
        $postulante = Postulante::create([
            'persona_id' => $persona->id,
            'colegio'    => $request->colegio,
            'estado'     => 'pendiente',
        ]);

        // 3. Crear Inscripción
        $inscripcion = Inscripcion::create([
            'postulante_id'     => $postulante->id,
            'periodo_id'        => $periodoActivo->id,
            'fecha_inscripcion' => now()->toDateString(),
            'estado'            => 'activa',
        ]);

        // 4. Crear postulaciones de carrera (prioridad 1 obligatoria, 2 opcional)
        PostulacionCarrera::create([
            'inscripcion_id' => $inscripcion->id,
            'carrera_id'     => $request->carrera_1,
            'prioridad'      => 1,
        ]);

        if ($request->filled('carrera_2')) {
            PostulacionCarrera::create([
                'inscripcion_id' => $inscripcion->id,
                'carrera_id'     => $request->carrera_2,
                'prioridad'      => 2,
            ]);
        }

        // 5. Actualizar estado del postulante a inscrito
        $postulante->update(['estado' => 'inscrito']);

        return redirect()->route('postulantes.index')
            ->with('success', "Postulante '{$persona->nombre}' registrado e inscrito correctamente.");
    }

    public function show(Postulante $postulante)
    {
        $postulante->load('persona', 'inscripciones.postulacionCarreras.carrera', 'inscripciones.periodo');
        return view('postulantes.show', compact('postulante'));
    }

    public function edit(Postulante $postulante)
    {
        $carreras = Carrera::where('activo', true)->orderBy('nombre')->get();
        $periodoActivo = Periodo::where('activo', true)->first();

        $inscripcionActiva = null;
        $carrera1 = null;
        $carrera2 = null;

        if ($periodoActivo) {
            $inscripcionActiva = Inscripcion::where('postulante_id', $postulante->id)
                ->where('periodo_id', $periodoActivo->id)
                ->with('postulacionCarreras')
                ->first();

            if ($inscripcionActiva) {
                $carrera1 = $inscripcionActiva->postulacionCarreras->where('prioridad', 1)->first();
                $carrera2 = $inscripcionActiva->postulacionCarreras->where('prioridad', 2)->first();
            }
        }

        $postulante->load('persona');
        return view('postulantes.edit', compact('postulante', 'carreras', 'carrera1', 'carrera2', 'inscripcionActiva'));
    }

    public function update(Request $request, Postulante $postulante)
    {
        $request->validate([
            'ci'               => 'required|string|max:20|unique:personas,ci,' . $postulante->persona_id,
            'nombre'           => 'required|string|max:200',
            'fecha_nacimiento' => 'nullable|date',
            'sexo'             => 'nullable|in:M,F',
            'direccion'        => 'nullable|string|max:255',
            'telefono'         => 'nullable|string|max:20',
            'correo'           => 'nullable|email|max:150|unique:personas,correo,' . $postulante->persona_id,
            'colegio'          => 'required|string|max:200',
            'carrera_1'        => 'required|exists:carreras,id',
            'carrera_2'        => 'nullable|exists:carreras,id|different:carrera_1',
        ], [
            'carrera_1.required'  => 'Debe seleccionar al menos una carrera (primera opción).',
            'carrera_2.different' => 'La segunda carrera debe ser diferente a la primera.',
        ]);

        // Actualizar Persona
        $postulante->persona->update([
            'ci'               => $request->ci,
            'nombre'           => $request->nombre,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'sexo'             => $request->sexo,
            'direccion'        => $request->direccion,
            'telefono'         => $request->telefono,
            'correo'           => $request->correo,
        ]);

        // Actualizar Postulante
        $postulante->update(['colegio' => $request->colegio]);

        // Actualizar carreras de la inscripción activa
        $periodoActivo = Periodo::where('activo', true)->first();
        if ($periodoActivo) {
            $inscripcion = Inscripcion::where('postulante_id', $postulante->id)
                ->where('periodo_id', $periodoActivo->id)
                ->first();

            if ($inscripcion) {
                // Borrar postulaciones anteriores y recrear
                $inscripcion->postulacionCarreras()->delete();

                PostulacionCarrera::create([
                    'inscripcion_id' => $inscripcion->id,
                    'carrera_id'     => $request->carrera_1,
                    'prioridad'      => 1,
                ]);

                if ($request->filled('carrera_2')) {
                    PostulacionCarrera::create([
                        'inscripcion_id' => $inscripcion->id,
                        'carrera_id'     => $request->carrera_2,
                        'prioridad'      => 2,
                    ]);
                }
            }
        }

        return redirect()->route('postulantes.index')
            ->with('success', "Postulante '{$postulante->persona->nombre}' actualizado correctamente.");
    }

    public function destroy(Postulante $postulante)
    {
        $nombre = $postulante->persona->nombre;
        // Eliminación lógica — cambiar estado
        $postulante->update(['estado' => 'pendiente']);

        return redirect()->route('postulantes.index')
            ->with('success', "Postulante '{$nombre}' marcado como pendiente.");
    }
}