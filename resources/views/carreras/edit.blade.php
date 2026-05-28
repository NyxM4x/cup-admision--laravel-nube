@extends('layouts.base')

@section('titulo', 'Editar Carrera')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-mortarboard-fill me-2"></i>Editar Carrera: {{ $carrera->nombre }}</h1>
    <p class="page-subtitle">Modificar los datos de la carrera y su cupo</p>
  </div>
  <a href="{{ route('carreras.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

@if($errors->any())
  <div class="alert alert-cup-danger">
    <i class="bi bi-exclamation-triangle me-2"></i>
    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
  </div>
@endif

<div class="panel-cup" style="max-width:720px">
  <div class="panel-cup-body">
    <form action="{{ route('carreras.update', $carrera) }}" method="POST">
      @csrf @method('PUT')

      <div class="mb-3">
        <label class="form-label">Código</label>
        <input type="text" name="codigo" class="form-control text-uppercase" value="{{ $carrera->codigo }}" required maxlength="20">
      </div>
      <div class="mb-3">
        <label class="form-label">Nombre de la Carrera</label>
        <input type="text" name="nombre" class="form-control" value="{{ $carrera->nombre }}" required maxlength="150">
      </div>
      <div class="mb-4">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-control" rows="3">{{ $carrera->descripcion }}</textarea>
      </div>

      @if($periodoActivo)
        <hr>
        <h6 class="mb-3" style="color:var(--cup-primary-light);font-weight:600;">
          <i class="bi bi-bar-chart me-2"></i>Cupo para el periodo activo
        </h6>
        <div class="mb-3">
          <label class="form-label">Cupo máximo de admitidos</label>
          <input type="number" name="cupo_max" class="form-control" value="{{ $cupoActual->cupo_max ?? old('cupo_max') }}" required min="1">
        </div>
        <div class="mb-4">
          <label class="form-label">Fecha COFI <small class="text-muted">(opcional)</small></label>
          <input type="date" name="fecha_cofi" class="form-control" value="{{ $cupoActual?->fecha_cofi?->format('Y-m-d') ?? '' }}">
        </div>
      @else
        <div class="alert alert-cup-warning"><i class="bi bi-exclamation-triangle me-2"></i>Sin periodo activo — no se puede editar el cupo.</div>
      @endif

      <hr>
      <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('carreras.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-cup-primary">
          <i class="bi bi-check-circle me-1"></i> Actualizar Carrera
        </button>
      </div>
    </form>
  </div>
</div>

@endsection
