<?php

namespace App\Http\Controllers;

use App\Models\DocumentoPostulante;
use App\Models\Inscripcion;
use App\Models\Requisito;
use App\Models\Periodo;
use App\Models\Postulante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentoPostulanteController extends Controller
{
    // Listado de postulantes con estado de documentación
    public function index()
    {
        $periodoActivo = Periodo::where('activo', true)->first();

        $inscripciones = collect();

        if ($periodoActivo) {
            $inscripciones = Inscripcion::where('periodo_id', $periodoActivo->id)
                ->where('estado', 'activa')
                ->with([
                    'postulante.persona',
                    'documentos.requisito',
                    'postulacionCarreras.carrera',
                ])
                ->get()
                ->map(function ($inscripcion) use ($periodoActivo) {
                    $requisitos   = Requisito::where('periodo_id', $periodoActivo->id)
                                            ->where('activo', true)->get();
                    $totalReqs    = $requisitos->count();
                    $totalSubidos = $inscripcion->documentos->count();
                    $aprobados    = $inscripcion->documentos->where('estado', 'aprobado')->count();
                    $rechazados   = $inscripcion->documentos->where('estado', 'rechazado')->count();

                    $inscripcion->total_requisitos = $totalReqs;
                    $inscripcion->total_subidos    = $totalSubidos;
                    $inscripcion->aprobados        = $aprobados;
                    $inscripcion->rechazados       = $rechazados;
                    $inscripcion->completo         = ($aprobados === $totalReqs && $totalReqs > 0);

                    return $inscripcion;
                });
        }

        return view('documentos.index', compact('inscripciones', 'periodoActivo'));
    }

    // Ver y gestionar documentos de una inscripción específica
    public function show(Inscripcion $inscripcion)
    {
        $periodoActivo = Periodo::where('activo', true)->first();

        // Requisitos del periodo activo
        $requisitos = Requisito::where('periodo_id', $inscripcion->periodo_id)
            ->where('activo', true)
            ->get();

        // Documentos ya subidos para esta inscripción
        $documentos = DocumentoPostulante::where('inscripcion_id', $inscripcion->id)
            ->with('requisito')
            ->get()
            ->keyBy('requisito_id'); // indexados por requisito para fácil acceso en la vista

        $inscripcion->load('postulante.persona', 'postulacionCarreras.carrera');

        return view('documentos.show', compact('inscripcion', 'requisitos', 'documentos'));
    }

    // Subir un documento para un requisito específico
    public function store(Request $request, Inscripcion $inscripcion)
{
    $requisito = Requisito::findOrFail($request->requisito_id);

    // Construir mimes correctamente — Laravel acepta: pdf,jpg,jpeg,png
    $formatosArray = array_map('trim', explode(',', strtolower($requisito->formato_aceptado)));
    $mimes = [];
    foreach ($formatosArray as $f) {
        if ($f === 'jpg') {
            $mimes[] = 'jpg';
            $mimes[] = 'jpeg';
        } else {
            $mimes[] = $f;
        }
    }
    $formatosMimes = implode(',', $mimes);
    $maxKb = $requisito->tamanio_max_kb;

    $request->validate([
        'requisito_id' => 'required|exists:requisitos,id',
        'archivo'      => "required|file|mimes:{$formatosMimes}|max:{$maxKb}",
    ], [
        'archivo.mimes' => "El archivo debe ser de tipo: {$requisito->formato_aceptado}",
        'archivo.max'   => "El archivo no debe superar " . ($maxKb / 1024) . " MB.",
    ]);

    // Si ya existe un documento para este requisito, eliminar el archivo anterior
    $docExistente = DocumentoPostulante::where('inscripcion_id', $inscripcion->id)
        ->where('requisito_id', $requisito->id)
        ->first();

    if ($docExistente) {
        Storage::disk('public')->delete($docExistente->archivo);
        $docExistente->delete();
    }

    // Guardar el nuevo archivo
    $path = $request->file('archivo')
        ->store("documentos/{$inscripcion->id}", 'public');

    DocumentoPostulante::create([
        'inscripcion_id' => $inscripcion->id,
        'requisito_id'   => $requisito->id,
        'archivo'        => $path,
        'estado'         => 'pendiente',
        'comentario'     => null,
        'fecha_subida'   => now(),
    ]);

    return redirect()->route('documentos.show', $inscripcion)
        ->with('success', "Documento '{$requisito->nombre}' subido correctamente.");
}

    // Aprobar un documento
    public function aprobar(DocumentoPostulante $documento)
    {
        $documento->update([
            'estado'     => 'aprobado',
            'comentario' => null,
        ]);

        return redirect()->route('documentos.show', $documento->inscripcion_id)
            ->with('success', "Documento '{$documento->requisito->nombre}' aprobado.");
    }

    // Rechazar un documento con comentario
    public function rechazar(Request $request, DocumentoPostulante $documento)
    {
        $request->validate([
            'comentario' => 'required|string|max:500',
        ], [
            'comentario.required' => 'Debe indicar el motivo del rechazo.',
        ]);

        $documento->update([
            'estado'     => 'rechazado',
            'comentario' => $request->comentario,
        ]);

        return redirect()->route('documentos.show', $documento->inscripcion_id)
            ->with('success', "Documento '{$documento->requisito->nombre}' rechazado.");
    }
}