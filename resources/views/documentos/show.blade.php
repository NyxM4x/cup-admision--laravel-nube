@extends('layouts.base')

@section('titulo', 'Validación de Documentación')

@section('contenido')

@php
  $persona = $inscripcion->postulante->persona;
  $carrera1 = $inscripcion->postulacionCarreras->where('prioridad', 1)->first();
  $carrera2 = $inscripcion->postulacionCarreras->where('prioridad', 2)->first();

  $totalReq = $requisitos->count();
  $cumplidos = $docPorRequisito->where('cumplido', true)->count();
  $habilitado = $totalReq > 0 && $cumplidos >= $totalReq;
@endphp

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1 class="mb-1">
      <i class="bi bi-folder2-open me-2"></i>
      Validación de Documentación
    </h1>
    <p class="text-muted mb-0">
      {{ $persona->nombre }} — CI: {{ $persona->ci }} · Inscripción #{{ $inscripcion->id }}
    </p>
  </div>
  <a href="{{ route('documentos.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

<div class="alert alert-info border-0" style="border-radius:8px">
  <i class="bi bi-postcard me-2"></i>
  <strong>Opción 1:</strong> {{ optional(optional($carrera1)->carrera)->nombre ?? '—' }}
  @if($carrera2)
    &nbsp;·&nbsp; <strong>Opción 2:</strong> {{ optional(optional($carrera2)->carrera)->nombre ?? '—' }}
  @endif
</div>

@if($habilitado)
  <div class="alert alert-cup-success">
    <i class="bi bi-check-circle-fill me-2"></i>
    <strong>¡Postulante habilitado!</strong> Todos los requisitos están validados.
    Se generó el usuario y se envió el email con las credenciales.
  </div>
@endif

<form method="POST" action="{{ route('documentos.actualizar', $inscripcion) }}">
  @csrf

  <div class="panel-cup">
    <div class="panel-cup-header">
      <strong>
        <i class="bi bi-check2-square me-1"></i>
        Requisitos a validar ({{ $cumplidos }}/{{ $totalReq }})
      </strong>
    </div>
    <div class="panel-cup-body">
      @forelse($requisitos as $req)
        @php
          $doc = $docPorRequisito->get($req->id);
          $checked = $doc && $doc->cumplido;
        @endphp
        <div class="form-check d-flex align-items-center gap-3 p-3 mb-2 border rounded"
             style="{{ $checked ? 'background: rgba(25, 135, 84, 0.06);' : '' }}">
          <input class="form-check-input" type="checkbox"
                 name="requisitos[{{ $req->id }}]" value="1"
                 id="req_{{ $req->id }}"
                 {{ $checked ? 'checked' : '' }}
                 style="width: 1.4em; height: 1.4em; cursor: pointer;">
          <label class="form-check-label flex-grow-1" for="req_{{ $req->id }}" style="cursor: pointer;">
            <div class="d-flex align-items-center gap-2">
              <strong>{{ $req->nombre }}</strong>
              @if($req->obligatorio)
                <span class="badge bg-danger">Obligatorio</span>
              @else
                <span class="badge bg-secondary">Opcional</span>
              @endif
            </div>
            @if($req->descripcion)
              <small class="text-muted d-block mt-1">{{ $req->descripcion }}</small>
            @endif
            @if($checked && $doc)
              <small class="text-success d-block mt-1">
                <i class="bi bi-check-circle"></i>
                Validado {{ optional($doc->fecha_validacion)->format('d/m/Y H:i') }}
              </small>
            @endif
          </label>
        </div>
      @empty
        <div class="text-muted text-center py-3">No hay requisitos configurados para este periodo.</div>
      @endforelse
    </div>
  </div>

  <div class="d-flex justify-content-between align-items-center mt-4">
    <a href="{{ route('documentos.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-x me-1"></i> Cancelar
    </a>
    <button type="submit" class="btn btn-cup-primary btn-lg">
      <i class="bi bi-save me-2"></i> Guardar validación
    </button>
  </div>
</form>

@endsection
