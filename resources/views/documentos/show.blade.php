@extends('layouts.base')

@section('titulo', 'Documentación del Postulante')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-folder me-2"></i>Documentación del Postulante</h1>
    <p class="page-subtitle">
      {{ $inscripcion->postulante->persona->nombre }}
      — CI: {{ $inscripcion->postulante->persona->ci }} · Inscripción #{{ $inscripcion->id }}
    </p>
  </div>
  <a href="{{ route('documentos.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

<div class="alert alert-info border-0 mb-4" style="border-radius:8px">
  <i class="bi bi-person-vcard me-2"></i>
  @foreach($inscripcion->postulacionCarreras as $pc)
    <strong>Opción {{ $pc->prioridad }}:</strong> {{ $pc->carrera->nombre }}{{ !$loop->last ? ' · ' : '' }}
  @endforeach
</div>

@foreach($requisitos as $requisito)
  @php $doc = $documentos->get($requisito->id); @endphp
  <div class="panel-cup mb-3">
    <div class="panel-cup-body">
      <div class="d-flex justify-content-between align-items-start mb-2">
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
            <small class="text-muted d-block">{{ $requisito->descripcion }}</small>
          @endif
          <small class="text-muted">
            Formatos: {{ $requisito->formato_aceptado }} | Máx: {{ $requisito->tamanio_max_kb / 1024 }} MB
          </small>
        </div>
        <div>
          @if($doc)
            @if($doc->estado === 'aprobado')
              <span class="badge-cup badge-activo"><i class="bi bi-check-circle me-1"></i>Aprobado</span>
            @elseif($doc->estado === 'rechazado')
              <span class="badge-cup badge-inactivo"><i class="bi bi-x-circle me-1"></i>Rechazado</span>
            @else
              <span class="badge-cup badge-warning-cup"><i class="bi bi-clock me-1"></i>Pendiente</span>
            @endif
          @else
            <span class="badge bg-secondary">Sin subir</span>
          @endif
        </div>
      </div>

      {{-- Documento ya subido --}}
      @if($doc)
        <div class="mt-2 p-2 rounded" style="background:#f8f9fb;border:1px solid var(--cup-border);">
          <small>
            <i class="bi bi-file-earmark-text me-1"></i>
            <a href="{{ Storage::url($doc->archivo) }}" target="_blank">Ver documento</a>
            | Subido: {{ $doc->fecha_subida?->format('d/m/Y H:i') }}
          </small>

          @if($doc->comentario)
            <div class="alert alert-cup-danger py-1 mt-2 mb-0">
              <small><i class="bi bi-chat-left-text me-1"></i>{{ $doc->comentario }}</small>
            </div>
          @endif

          {{-- Botones Aprobar / Rechazar (solo si está pendiente o rechazado) --}}
          @if($doc->estado !== 'aprobado')
            <div class="mt-2 d-flex gap-2 flex-wrap">
              <form action="{{ route('documentos.aprobar', $doc) }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-success"><i class="bi bi-check-circle me-1"></i>Aprobar</button>
              </form>
              <button class="btn btn-sm btn-danger" data-bs-toggle="collapse" data-bs-target="#rechazar-{{ $doc->id }}">
                <i class="bi bi-x-circle me-1"></i>Rechazar
              </button>
            </div>

            <div class="collapse mt-2" id="rechazar-{{ $doc->id }}">
              <form action="{{ route('documentos.rechazar', $doc) }}" method="POST">
                @csrf
                <div class="mb-2">
                  <textarea name="comentario" class="form-control form-control-sm" rows="2" placeholder="Motivo del rechazo..." required></textarea>
                </div>
                <button class="btn btn-sm btn-danger">Confirmar rechazo</button>
              </form>
            </div>
          @endif
        </div>
      @endif

      {{-- Formulario para subir / reemplazar documento --}}
      <div class="mt-3">
        <form action="{{ route('documentos.store', $inscripcion) }}" method="POST" enctype="multipart/form-data"
              class="d-flex gap-2 align-items-center flex-wrap">
          @csrf
          <input type="hidden" name="requisito_id" value="{{ $requisito->id }}">
          <input type="file" name="archivo" class="form-control form-control-sm" style="max-width:300px" accept=".pdf,.jpg,.jpeg,.png" required>
          <button class="btn btn-sm btn-cup-primary">
            <i class="bi {{ $doc ? 'bi-arrow-repeat' : 'bi-upload' }} me-1"></i>{{ $doc ? 'Reemplazar' : 'Subir' }}
          </button>
        </form>
        @if($errors->has('archivo') && old('requisito_id') == $requisito->id)
          <small class="text-danger">{{ $errors->first('archivo') }}</small>
        @endif
      </div>
    </div>
  </div>
@endforeach

@endsection
