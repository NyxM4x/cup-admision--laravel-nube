<?php

namespace App\Http\Controllers;

use App\Domain\Bitacora\Services\BitacoraLogger;
use App\Mail\HabilitacionPostulanteMail;
use App\Models\DocumentoPostulante;
use App\Models\Inscripcion;
use App\Models\Periodo;
use App\Models\Requisito;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class DocumentoPostulanteController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->input('q', ''));

        $periodoActivo = Periodo::where('activo', true)->orderBy('id', 'desc')->first();
        $periodoId = $request->input('periodo_id', $periodoActivo?->id);
        $periodos = Periodo::orderBy('id', 'desc')->get();

        $query = Inscripcion::with([
                'postulante.persona',
                'postulacionCarreras.carrera',
                'documentos.requisito',
            ])
            ->where('estado', 'activa')
            ->orderBy('id', 'desc');

        if ($periodoId && $periodoId !== 'todos') {
            $query->where('periodo_id', $periodoId);
        }

        if ($q !== '') {
            $query->whereHas('postulante.persona', function ($w) use ($q) {
                $w->whereRaw('unaccent(nombre) ilike unaccent(?)', ["%{$q}%"])
                  ->orWhere('ci', 'ilike', "%{$q}%");
            });
        }

        $inscripciones = $query->paginate(20)->withQueryString();

        return view('documentos.index', compact('inscripciones', 'q', 'periodos', 'periodoId'));
    }

    public function show(Inscripcion $inscripcion)
    {
        $inscripcion->load([
            'postulante.persona',
            'postulacionCarreras.carrera',
            'documentos.requisito',
        ]);

        // Todos los requisitos del periodo (no solo los ya marcados)
        $requisitos = Requisito::where('periodo_id', $inscripcion->periodo_id)
            ->orderBy('obligatorio', 'desc')
            ->orderBy('nombre')
            ->get();

        $docPorRequisito = $inscripcion->documentos->keyBy('requisito_id');

        return view('documentos.show', compact('inscripcion', 'requisitos', 'docPorRequisito'));
    }

    public function actualizar(Request $request, Inscripcion $inscripcion)
    {
        $request->validate([
            'requisitos'   => 'array',
            'requisitos.*' => 'boolean',
        ]);

        $habilitadoAhora = false;

        DB::transaction(function () use ($request, $inscripcion, &$habilitadoAhora) {
            $requisitos = Requisito::where('periodo_id', $inscripcion->periodo_id)->get();

            foreach ($requisitos as $req) {
                $cumplido = (bool) $request->input('requisitos.'.$req->id, false);

                DocumentoPostulante::updateOrCreate(
                    [
                        'inscripcion_id' => $inscripcion->id,
                        'requisito_id'   => $req->id,
                    ],
                    [
                        'cumplido'         => $cumplido,
                        'fecha_validacion' => $cumplido ? now() : null,
                        'validado_por'     => $cumplido ? Auth::id() : null,
                        'estado'           => $cumplido ? 'aprobado' : 'pendiente', // compat columna vieja
                    ]
                );
            }

            // ¿Todos los OBLIGATORIOS cumplidos?
            $obligatorios = Requisito::where('periodo_id', $inscripcion->periodo_id)
                ->where('obligatorio', true)
                ->pluck('id');

            $cumplidosObligatorios = DocumentoPostulante::where('inscripcion_id', $inscripcion->id)
                ->whereIn('requisito_id', $obligatorios)
                ->where('cumplido', true)
                ->count();

            $todosCumplidos = $obligatorios->count() > 0
                && $obligatorios->count() === $cumplidosObligatorios;

            if ($todosCumplidos) {
                // Retorna true solo si CREÓ el usuario (primera habilitación)
                $habilitadoAhora = $this->habilitarPostulante($inscripcion);
            }
        });

        if ($habilitadoAhora) {
            return redirect()->route('documentos.show', $inscripcion)
                ->with('success', '¡Postulante habilitado! Se generó el usuario y se envió el email con las credenciales.');
        }

        return redirect()->route('documentos.show', $inscripcion)
            ->with('success', 'Documentación actualizada correctamente.');
    }

    protected function habilitarPostulante(Inscripcion $inscripcion): bool
    {
        $postulante = $inscripcion->postulante;
        $persona = $postulante->persona;

        // Idempotente: si ya tiene usuario, no duplicar ni reenviar
        if (User::where('email', $persona->correo)->exists()) {
            return false;
        }

        $passwordTemporal = $this->generarPasswordTemporal();
        $rolPostulante = Rol::where('nombre', 'Postulante')->first();

        User::create([
            'name'                  => $persona->nombre,
            'email'                 => $persona->correo,
            'ci'                    => $persona->ci,
            'password'              => Hash::make($passwordTemporal),
            'rol_id'                => $rolPostulante?->id,
            'activo'                => true,
            'debe_cambiar_password' => true, // fuerza cambio en primer login
        ]);

        $carrera1 = optional(optional($inscripcion->postulacionCarreras->where('prioridad', 1)->first())->carrera)->nombre ?? 'Sin asignar';

        try {
            Mail::to($persona->correo)->send(new HabilitacionPostulanteMail(
                nombreUsuario: $persona->nombre,
                email: $persona->correo,
                passwordTemporal: $passwordTemporal,
                carrera1: $carrera1,
            ));
        } catch (\Exception $e) {
            Log::error('Error enviando mail de habilitacion: '.$e->getMessage());
        }

        BitacoraLogger::registrar(
            'POSTULANTE_HABILITADO',
            'Documentos',
            "Postulante {$persona->nombre} habilitado para pago (CI: {$persona->ci})",
            Auth::id()
        );

        return true;
    }

    protected function generarPasswordTemporal(): string
    {
        // 8 chars: 1 mayúscula + 1 minúscula + 1 número + 5 aleatorios
        $may = chr(rand(65, 90));
        $min = chr(rand(97, 122));
        $num = (string) rand(0, 9);
        $resto = Str::random(5);

        return $may.$min.$num.$resto;
    }
}
