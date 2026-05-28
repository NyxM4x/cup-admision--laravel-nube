@extends('layouts.base')

@section('titulo', 'Editar Periodo')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-calendar3 me-2"></i>Editar Periodo Académico</h1>
    <p class="page-subtitle">Modificar las fechas de inscripción y del curso</p>
  </div>
  <a href="{{ route('periodos.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

@if($errors->any())
  <div class="alert alert-cup-danger">
    <i class="bi bi-exclamation-triangle me-2"></i>
    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
  </div>
@endif

<div class="panel-cup" style="max-width:900px">
  <div class="panel-cup-body">
    <form action="{{ route('periodos.update', $periodo) }}" method="POST">
      @csrf @method('PUT')

      <h6 class="mb-3" style="color:var(--cup-primary-light);font-weight:600;">
        <i class="bi bi-calendar-check me-2"></i>Fechas de Inscripción
      </h6>
      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label">Inicio de Inscripción</label>
          <input type="date" name="fecha_ini_inscripcion" class="form-control" value="{{ $periodo->fecha_ini_inscripcion->format('Y-m-d') }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Fin de Inscripción</label>
          <input type="date" name="fecha_fin_inscripcion" class="form-control" value="{{ $periodo->fecha_fin_inscripcion->format('Y-m-d') }}" required>
        </div>
      </div>

      <hr>
      <h6 class="mb-3" style="color:var(--cup-primary-light);font-weight:600;">
        <i class="bi bi-calendar-event me-2"></i>Fechas del Curso
      </h6>
      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label">Inicio del Curso</label>
          <input type="date" name="fecha_ini_curso" class="form-control" value="{{ $periodo->fecha_ini_curso->format('Y-m-d') }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Fin del Curso</label>
          <input type="date" name="fecha_fin_curso" class="form-control" value="{{ $periodo->fecha_fin_curso->format('Y-m-d') }}" required>
        </div>
      </div>

      <hr>
      <div class="form-check mb-4">
        <input type="checkbox" name="activo" class="form-check-input" id="activo" {{ $periodo->activo ? 'checked' : '' }}>
        <label class="form-check-label" for="activo">Marcar como periodo activo</label>
      </div>

      <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('periodos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-cup-primary">
          <i class="bi bi-check-circle me-1"></i> Actualizar Periodo
        </button>
      </div>
    </form>
  </div>
</div>

@endsection
