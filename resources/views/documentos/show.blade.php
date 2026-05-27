<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documentos del Postulante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4" style="max-width:800px">

    <a href="{{ route('documentos.index') }}" class="btn btn-secondary mb-3">← Volver</a>

    {{-- Info del postulante --}}
    <div class="card p-3 mb-4 bg-white">
        <h5 class="text-primary mb-1">
            🎓 {{ $inscripcion->postulante->persona->nombre }}
        </h5>
        <small class="text-muted">
            CI: {{ $inscripcion->postulante->persona->ci }} |
            Inscripción #{{ $inscripcion->id }} |
            @foreach($inscripcion->postulacionCarreras as $pc)
                Opción {{ $pc->prioridad }}: {{ $pc->carrera->nombre }}{{ !$loop->last ? ' | ' : '' }}
            @endforeach
        </small>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Lista de requisitos con su documento --}}
    @foreach($requisitos as $requisito)
    @php $doc = $documentos->get($requisito->id); @endphp
    <div class="card mb-3 bg-white">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">
                        {{ $requisito->nombre }}
                        @if($requisito->obligatorio)
                            <span class="badge bg-danger">Obligatorio</span>
                        @else
                            <span class="badge bg-secondary">Opcional</span>
                        @endif
                    </h6>
                    @if($requisito->descripcion)
                        <small class="text-muted">{{ $requisito->descripcion }}</small><br>
                    @endif
                    <small class="text-muted">
                        Formatos: {{ $requisito->formato_aceptado }} |
                        Máx: {{ $requisito->tamanio_max_kb / 1024 }} MB
                    </small>
                </div>
                <div>
                    @if($doc)
                        @if($doc->estado === 'aprobado')
                            <span class="badge bg-success fs-6">✅ Aprobado</span>
                        @elseif($doc->estado === 'rechazado')
                            <span class="badge bg-danger fs-6">❌ Rechazado</span>
                        @else
                            <span class="badge bg-warning text-dark fs-6">⏳ Pendiente</span>
                        @endif
                    @else
                        <span class="badge bg-secondary fs-6">Sin subir</span>
                    @endif
                </div>
            </div>

            {{-- Documento ya subido --}}
            @if($doc)
                <div class="mt-2 p-2 bg-light rounded">
                    <small>
                        📄 <a href="{{ Storage::url($doc->archivo) }}" target="_blank">Ver documento</a>
                        | Subido: {{ $doc->fecha_subida?->format('d/m/Y H:i') }}
                    </small>

                    @if($doc->comentario)
                        <div class="alert alert-danger py-1 mt-1 mb-0">
                            <small>💬 {{ $doc->comentario }}</small>
                        </div>
                    @endif

                    {{-- Botones Aprobar / Rechazar (solo si está pendiente o rechazado) --}}
                    @if($doc->estado !== 'aprobado')
                        <div class="mt-2 d-flex gap-2 flex-wrap">
                            {{-- Aprobar --}}
                            <form action="{{ route('documentos.aprobar', $doc) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-success">✅ Aprobar</button>
                            </form>

                            {{-- Rechazar con motivo --}}
                            <button class="btn btn-sm btn-danger"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#rechazar-{{ $doc->id }}">
                                ❌ Rechazar
                            </button>
                        </div>

                        <div class="collapse mt-2" id="rechazar-{{ $doc->id }}">
                            <form action="{{ route('documentos.rechazar', $doc) }}" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <textarea name="comentario" class="form-control form-control-sm"
                                              rows="2" placeholder="Motivo del rechazo..." required></textarea>
                                </div>
                                <button class="btn btn-sm btn-danger">Confirmar rechazo</button>
                            </form>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Formulario para subir / reemplazar documento --}}
            <div class="mt-3">
                <form action="{{ route('documentos.store', $inscripcion) }}"
                      method="POST" enctype="multipart/form-data"
                      class="d-flex gap-2 align-items-center flex-wrap">
                    @csrf
                    <input type="hidden" name="requisito_id" value="{{ $requisito->id }}">
                    <input type="file" name="archivo" class="form-control form-control-sm"
                           style="max-width:300px"
                           accept=".pdf,.jpg,.jpeg,.png"
                           required>
                    <button class="btn btn-sm btn-primary">
                        {{ $doc ? '🔄 Reemplazar' : '📤 Subir' }}
                    </button>
                </form>
                {{-- Mostrar error solo del requisito actual --}}
                @if($errors->has('archivo') && old('requisito_id') == $requisito->id)
                    <small class="text-danger">{{ $errors->first('archivo') }}</small>
                @endif
            </div>

        </div>
    </div>
    @endforeach

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>