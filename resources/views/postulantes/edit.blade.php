@extends('layouts.base')

@section('titulo', 'Editar Postulante')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-pencil-square me-2"></i>Editar Postulante: {{ $postulante->persona->nombre }}</h1>
    <p class="page-subtitle">Modificar datos del postulante y sus carreras de postulación</p>
  </div>
  <a href="{{ route('postulantes.index') }}" class="btn btn-outline-secondary">
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
    <form action="{{ route('postulantes.update', $postulante) }}" method="POST">
      @csrf @method('PUT')

      <h5 class="mb-3" style="color:var(--cup-primary-light);font-weight:600;">
        <i class="bi bi-clipboard me-2"></i>Datos Personales
      </h5>

      <div class="row g-3 mb-3">
        <div class="col-md-4">
          <label class="form-label">CI <span class="text-danger">*</span></label>
          <input type="text" name="ci" class="form-control" value="{{ old('ci', $postulante->persona->ci) }}" required maxlength="20">
        </div>
        <div class="col-md-8">
          <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
          <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $postulante->persona->nombre) }}" required maxlength="200">
        </div>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label">Fecha de nacimiento</label>
          <input type="date" name="fecha_nacimiento" class="form-control"
                 value="{{ old('fecha_nacimiento', optional($postulante->persona->fecha_nacimiento)->format('Y-m-d')) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Sexo</label>
          <select name="sexo" class="form-select">
            <option value="">— Seleccionar —</option>
            <option value="M" {{ old('sexo', $postulante->persona->sexo) == 'M' ? 'selected' : '' }}>Masculino</option>
            <option value="F" {{ old('sexo', $postulante->persona->sexo) == 'F' ? 'selected' : '' }}>Femenino</option>
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Dirección</label>
        <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $postulante->persona->direccion) }}" maxlength="255">
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label">Teléfono</label>
          <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $postulante->persona->telefono) }}" maxlength="20">
        </div>
        <div class="col-md-6">
          <label class="form-label">Correo electrónico</label>
          <input type="email" name="correo" class="form-control" value="{{ old('correo', $postulante->persona->correo) }}" maxlength="150">
        </div>
      </div>

      <hr>

      <h5 class="mb-3" style="color:var(--cup-primary-light);font-weight:600;">
        <i class="bi bi-building me-2"></i>Datos del Postulante
      </h5>

      <div class="mb-4">
        <label class="form-label">Colegio de procedencia <span class="text-danger">*</span></label>
        <input type="text" name="colegio" class="form-control" value="{{ old('colegio', $postulante->colegio) }}" required maxlength="200">
      </div>

      <hr>

      <h5 class="mb-3" style="color:var(--cup-primary-light);font-weight:600;">
        <i class="bi bi-mortarboard me-2"></i>Carreras a Postular
      </h5>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label">1ra opción <span class="text-danger">*</span></label>
          <select name="carrera_1" class="form-select" required>
            <option value="">— Seleccionar carrera —</option>
            @foreach($carreras as $carrera)
              <option value="{{ $carrera->id }}" {{ old('carrera_1', $carrera1?->carrera_id) == $carrera->id ? 'selected' : '' }}>
                {{ $carrera->codigo }} — {{ $carrera->nombre }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">2da opción <small class="text-muted">(opcional)</small></label>
          <select name="carrera_2" class="form-select">
            <option value="">— Sin segunda opción —</option>
            @foreach($carreras as $carrera)
              <option value="{{ $carrera->id }}" {{ old('carrera_2', $carrera2?->carrera_id) == $carrera->id ? 'selected' : '' }}>
                {{ $carrera->codigo }} — {{ $carrera->nombre }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <hr>

      <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('postulantes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-cup-primary">
          <i class="bi bi-check-circle me-1"></i> Actualizar Postulante
        </button>
      </div>
    </form>
  </div>
</div>

@endsection
