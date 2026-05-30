<?php

namespace App\Http\Controllers;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Models\Postulante;
use App\Models\Persona;
use App\Models\Inscripcion;
use App\Models\PostulacionCarrera;
use App\Models\Carrera;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PostulanteController extends Controller
{
    public function index(Request $request)
    {
        $periodoActivo = Periodo::where('activo', true)->orderBy('id', 'desc')->first();

        $q = trim($request->input('q', ''));
        $estado = $request->input('estado', 'activos'); // activos|inactivos|todos

        // Periodo: por defecto el activo más reciente; 'todos' = sin filtro
        $periodoId = $request->input('periodo_id', $periodoActivo?->id);
        $periodos = Periodo::orderBy('id', 'desc')->get();

        // Cargar la inscripción del periodo filtrado para mostrar carreras correctas
        $query = Postulante::with([
                'persona',
                'inscripciones' => function ($w) use ($periodoId) {
                    if ($periodoId && $periodoId !== 'todos') {
                        $w->where('periodo_id', $periodoId);
                    }
                    $w->with('postulacionCarreras.carrera', 'periodo');
                },
            ])
            ->orderBy('created_at', 'desc');

        // Filtro de estado lógico (si la columna existe)
        if (Schema::hasColumn('postulantes', 'activo')) {
            if ($estado === 'activos') {
                $query->where('activo', true);
            } elseif ($estado === 'inactivos') {
                $query->where('activo', false);
            }
        }

        // Filtro de periodo (vía inscripciones)
        if ($periodoId && $periodoId !== 'todos') {
            $query->whereHas('inscripciones', function ($w) use ($periodoId) {
                $w->where('periodo_id', $periodoId);
            });
        }

        // Buscador por nombre, CI o correo de la persona
        if ($q !== '') {
            $query->whereHas('persona', function ($w) use ($q) {
                $w->whereRaw('unaccent(nombre) ilike unaccent(?)', ["%{$q}%"])
                  ->orWhere('ci', 'ilike', "%{$q}%")
                  ->orWhereRaw('unaccent(correo) ilike unaccent(?)', ["%{$q}%"]);
            });
        }

        $postulantes = $query->paginate(20)->withQueryString();

        return view('postulantes.index', compact('postulantes', 'periodoActivo', 'q', 'estado', 'periodos', 'periodoId'));
    }

    // Endpoint AJAX: verifica si el CI ya existe y devuelve datos + inscripciones
    public function verificarCI(Request $request)
    {
        $ci = trim($request->input('ci', ''));

        if (strlen($ci) < 4) {
            return response()->json(['existe' => false]);
        }

        $persona = Persona::where('ci', $ci)->first();
        if (! $persona) {
            return response()->json(['existe' => false]);
        }

        $postulante = Postulante::where('persona_id', $persona->id)->first();

        $inscripciones = [];
        if ($postulante) {
            $inscripciones = Inscripcion::with('periodo')
                ->where('postulante_id', $postulante->id)
                ->orderBy('id', 'desc')
                ->get()
                ->map(fn ($i) => [
                    'id'      => $i->id,
                    'periodo' => 'Periodo #'.$i->periodo_id
                                 .($i->periodo ? ' ('.optional($i->periodo->fecha_ini_curso)->format('d/m/Y').')' : ''),
                    'estado'  => $i->estado,
                ]);
        }

        return response()->json([
            'existe'  => true,
            'persona' => [
                'id'               => $persona->id,
                'ci'               => $persona->ci,
                'nombre'           => $persona->nombre,
                'correo'           => $persona->correo,
                'fecha_nacimiento' => $persona->fecha_nacimiento,
                'sexo'             => $persona->sexo,
                'telefono'         => $persona->telefono,
                'direccion'        => $persona->direccion,
            ],
            'postulante' => $postulante ? [
                'id'      => $postulante->id,
                'activo'  => $postulante->activo ?? true,
                'colegio' => $postulante->colegio,
            ] : null,
            'inscripciones' => $inscripciones,
        ]);
    }

    public function archivar(Postulante $postulante)
    {
        $postulante->update(['activo' => false]);

        BitacoraLogger::registrar(
            'POSTULANTE_ARCHIVADO',
            'Postulantes',
            "Postulante {$postulante->persona->nombre} archivado (CI: {$postulante->persona->ci})",
            Auth::id()
        );

        return back()->with('success', 'Postulante archivado correctamente.');
    }

    public function reactivar(Postulante $postulante)
    {
        $postulante->update(['activo' => true]);

        BitacoraLogger::registrar(
            'POSTULANTE_REACTIVADO',
            'Postulantes',
            "Postulante {$postulante->persona->nombre} reactivado (CI: {$postulante->persona->ci})",
            Auth::id()
        );

        return back()->with('success', 'Postulante reactivado correctamente.');
    }

    public function create()
    {
        $periodoActivo = Periodo::where('activo', true)->first();
        $carreras      = Carrera::where('activo', true)->orderBy('nombre')->get();

        return view('postulantes.create', compact('periodoActivo', 'carreras'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // Datos Persona (ci SIN unique: se permite reinscripción)
            'ci'               => 'required|string|max:20',
            'nombre'           => 'required|string|max:200',
            'fecha_nacimiento' => 'nullable|date',
            'sexo'             => 'nullable|in:M,F',
            'direccion'        => 'nullable|string|max:255',
            'telefono'         => 'nullable|string|max:20',
            'correo'           => 'nullable|email|max:150',
            // Datos Postulante
            'colegio'          => 'required|string|max:200',
            // Carreras
            'carrera_1'        => 'required|exists:carreras,id',
            'carrera_2'        => 'nullable|exists:carreras,id|different:carrera_1',
        ], [
            'carrera_1.required'  => 'Debe seleccionar al menos una carrera (primera opción).',
            'carrera_2.different' => 'La segunda carrera debe ser diferente a la primera.',
        ]);

        $periodoActivo = Periodo::where('activo', true)->orderBy('id', 'desc')->first();
        if (! $periodoActivo) {
            return back()->withInput()->withErrors([
                'general' => 'No existe un periodo académico activo. Creá uno antes de inscribir postulantes.',
            ]);
        }

        // Guard temprano: si la persona ya está inscrita en el periodo activo, no duplicar
        $personaExistente = Persona::where('ci', $request->ci)->first();
        if ($personaExistente) {
            $postExistente = Postulante::where('persona_id', $personaExistente->id)->first();
            if ($postExistente && Inscripcion::where('postulante_id', $postExistente->id)
                    ->where('periodo_id', $periodoActivo->id)->exists()) {
                return back()->withInput()->withErrors([
                    'ci' => 'Este postulante ya tiene una inscripción en el periodo activo. No se puede duplicar.',
                ]);
            }
        }

        $reinscripcion = false;

        DB::transaction(function () use ($request, $periodoActivo, &$reinscripcion) {
            // 1) Persona: reutilizar si el CI existe, si no crear
            $persona = Persona::where('ci', $request->ci)->first();
            if ($persona) {
                $persona->update([
                    'nombre'    => $request->nombre,
                    'correo'    => $request->correo ?: $persona->correo,
                    'telefono'  => $request->telefono ?: $persona->telefono,
                    'direccion' => $request->direccion ?: $persona->direccion,
                ]);
                $reinscripcion = true;
            } else {
                $persona = Persona::create([
                    'ci'               => $request->ci,
                    'nombre'           => $request->nombre,
                    'correo'           => $request->correo,
                    'fecha_nacimiento' => $request->fecha_nacimiento,
                    'sexo'             => $request->sexo,
                    'telefono'         => $request->telefono,
                    'direccion'        => $request->direccion,
                ]);
            }

            // 2) Postulante: reutilizar o crear; reactivar si estaba archivado
            $postulante = Postulante::firstOrCreate(
                ['persona_id' => $persona->id],
                ['colegio' => $request->colegio, 'estado' => 'inscrito', 'activo' => true]
            );
            if (! ($postulante->activo ?? true)) {
                $postulante->update(['activo' => true, 'colegio' => $request->colegio]);
            }

            // 3) Inscripción al periodo activo
            $inscripcion = Inscripcion::create([
                'postulante_id'     => $postulante->id,
                'periodo_id'        => $periodoActivo->id,
                'fecha_inscripcion' => now()->toDateString(),
                'estado'            => 'activa',
            ]);

            // 4) Postulaciones de carrera
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

            // 5) Bitácora
            BitacoraLogger::registrar(
                $reinscripcion ? 'POSTULANTE_REINSCRITO' : 'POSTULANTE_CREADO',
                'Postulantes',
                ($reinscripcion ? 'Reinscripción: ' : 'Inscripción nueva: ')
                    ."{$persona->nombre} (CI {$persona->ci}) al periodo #{$periodoActivo->id}",
                Auth::id()
            );
        });

        return redirect()->route('postulantes.index')->with('success',
            $reinscripcion
                ? '¡Reinscripción exitosa! La persona ya existía y se creó una nueva inscripción en el periodo activo.'
                : 'Postulante registrado e inscrito correctamente.'
        );
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

        BitacoraLogger::registrar(
            'EDITAR',
            'Postulantes',
            'Postulante editado: '.$postulante->persona->nombre.' ID='.$postulante->id
        );

        return redirect()->route('postulantes.index')
            ->with('success', "Postulante '{$postulante->persona->nombre}' actualizado correctamente.");
    }

    public function destroy(Postulante $postulante)
    {
        $nombre = $postulante->persona->nombre;
        // Eliminación lógica — cambiar estado
        $postulante->update(['estado' => 'pendiente']);

        BitacoraLogger::registrar(
            'DESACTIVAR',
            'Postulantes',
            'Postulante marcado como pendiente: '.$nombre.' ID='.$postulante->id
        );

        return redirect()->route('postulantes.index')
            ->with('success', "Postulante '{$nombre}' marcado como pendiente.");
    }
}