@extends('layouts.base')

@section('titulo', 'Registrar Postulante')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-person-plus-fill me-2"></i>Registrar Postulante</h1>
    <p class="page-subtitle">Completar los datos personales, académicos y carreras a postular</p>
  </div>
  <a href="{{ route('postulantes.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

@if(!$periodoActivo)
  <div class="alert alert-warning border-0" style="border-radius:8px">
    <i class="bi bi-exclamation-triangle me-2"></i>
    No hay periodo activo. <a href="{{ route('periodos.create') }}" class="alert-link">Crear periodo primero</a>
  </div>
@endif

@if($errors->any())
  <div class="alert alert-cup-danger">
    <i class="bi bi-exclamation-triangle me-2"></i>
    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
  </div>
@endif

<div class="panel-cup" style="max-width:900px">
  <div class="panel-cup-body">
    <form action="{{ route('postulantes.store') }}" method="POST">
      @csrf

      <h5 class="mb-3" style="color:var(--cup-primary-light);font-weight:600;">
        <i class="bi bi-clipboard me-2"></i>Datos Personales
      </h5>

      <div class="row g-3 mb-3">
        <div class="col-md-4">
          <label class="form-label">CI <span class="text-danger">*</span></label>
          <input type="text" name="ci" class="form-control" value="{{ old('ci') }}" required maxlength="20" placeholder="ej: 7654321">
        </div>
        <div class="col-md-8">
          <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
          <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required maxlength="200" placeholder="ej: María Fernanda Rojas">
        </div>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label">Fecha de nacimiento</label>
          <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Sexo</label>
          <select name="sexo" class="form-select">
            <option value="">— Seleccionar —</option>
            <option value="M" {{ old('sexo') == 'M' ? 'selected' : '' }}>Masculino</option>
            <option value="F" {{ old('sexo') == 'F' ? 'selected' : '' }}>Femenino</option>
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Dirección</label>
        <input type="text" name="direccion" class="form-control" value="{{ old('direccion') }}" maxlength="255" placeholder="ej: Barrio Las Palmas, Calle 5 #123">
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label">Teléfono</label>
          <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}" maxlength="20" placeholder="ej: 76543210">
        </div>
        <div class="col-md-6">
          <label class="form-label">Correo electrónico</label>
          <input type="email" name="correo" class="form-control" value="{{ old('correo') }}" maxlength="150" placeholder="ej: maria@gmail.com">
        </div>
      </div>

      <hr>

      <h5 class="mb-3" style="color:var(--cup-primary-light);font-weight:600;">
        <i class="bi bi-building me-2"></i>Datos del Postulante
      </h5>

      <div class="mb-4">
        <label class="form-label">Colegio de procedencia <span class="text-danger">*</span></label>
        <input type="text" name="colegio" class="form-control" value="{{ old('colegio') }}" required maxlength="200" placeholder="ej: U.E. San Calixto">
      </div>

      <hr>

      <h5 class="mb-2" style="color:var(--cup-primary-light);font-weight:600;">
        <i class="bi bi-mortarboard me-2"></i>Carreras a Postular
      </h5>
      <p class="text-muted small mb-3">La primera opción es obligatoria. Si los cupos de la 1ra se llenan, se asignará la 2da.</p>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label">1ra opción <span class="text-danger">*</span></label>
          <select name="carrera_1" class="form-select" required>
            <option value="">— Seleccionar carrera —</option>
            @foreach($carreras as $carrera)
              <option value="{{ $carrera->id }}" {{ old('carrera_1') == $carrera->id ? 'selected' : '' }}>
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
              <option value="{{ $carrera->id }}" {{ old('carrera_2') == $carrera->id ? 'selected' : '' }}>
                {{ $carrera->codigo }} — {{ $carrera->nombre }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <hr>

      <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('postulantes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-cup-primary" {{ !$periodoActivo ? 'disabled' : '' }}>
          <i class="bi bi-check-circle me-1"></i> Registrar e Inscribir Postulante
        </button>
      </div>
    </form>
  </div>
</div>

@endsection
