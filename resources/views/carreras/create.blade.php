@extends('layouts.base')

@section('titulo', 'Nueva Carrera')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-mortarboard-fill me-2"></i>Nueva Carrera</h1>
    <p class="page-subtitle">Registrar una carrera y su cupo en el periodo activo</p>
  </div>
  <a href="{{ route('carreras.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

@if(!$periodoActivo)
  <div class="alert alert-cup-warning">
    <i class="bi bi-exclamation-triangle me-2"></i>No hay periodo activo. No puedes registrar carreras sin un periodo activo.
    <a href="{{ route('periodos.create') }}" class="alert-link">Crear periodo primero</a>
  </div>
@else
  <div class="alert alert-info border-0" style="border-radius:8px">
    <i class="bi bi-calendar-event me-2"></i>Se asociará al periodo activo:
    <strong>{{ $periodoActivo->fecha_ini_inscripcion->format('d/m/Y') }}</strong>
  </div>
@endif

@if($errors->any())
  <div class="alert alert-cup-danger">
    <i class="bi bi-exclamation-triangle me-2"></i>
    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
  </div>
@endif

<div class="panel-cup" style="max-width:720px">
  <div class="panel-cup-body">
    <form action="{{ route('carreras.store') }}" method="POST">
      @csrf

      <div class="mb-3">
        <label class="form-label">Código <small class="text-muted">(ej: ING-COMP)</small></label>
        <input type="text" name="codigo" class="form-control text-uppercase" value="{{ old('codigo') }}" required maxlength="20">
      </div>
      <div class="mb-3">
        <label class="form-label">Nombre de la Carrera</label>
        <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required maxlength="150">
      </div>
      <div class="mb-4">
        <label class="form-label">Descripción <small class="text-muted">(opcional)</small></label>
        <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
      </div>

      <hr>
      <h6 class="mb-3" style="color:var(--cup-primary-light);font-weight:600;">
        <i class="bi bi-bar-chart me-2"></i>Cupo para el periodo activo
      </h6>
      <div class="mb-3">
        <label class="form-label">Cupo máximo de admitidos</label>
        <input type="number" name="cupo_max" class="form-control" value="{{ old('cupo_max') }}" required min="1">
        <small class="text-muted">Este valor es el INPUT del algoritmo de ranking (CU23/CU24)</small>
      </div>
      <div class="mb-3">
        <label class="form-label">Monto de inscripción (Bs.)</label>
        <input type="number" name="monto_inscripcion" class="form-control"
              value="{{ old('monto_inscripcion', 50) }}" required min="1" step="0.01">
        <small class="text-muted">Monto que el postulante pagará para inscribirse a esta carrera</small>
      </div>
      <div class="mb-4">
        <label class="form-label">Fecha COFI <small class="text-muted">(opcional)</small></label>
        <input type="date" name="fecha_cofi" class="form-control" value="{{ old('fecha_cofi') }}">
      </div>

      <hr>
      <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('carreras.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-cup-primary" {{ !$periodoActivo ? 'disabled' : '' }}>
          <i class="bi bi-check-circle me-1"></i> Guardar Carrera
        </button>
      </div>
    </form>
  </div>
</div>

@endsection
