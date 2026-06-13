@extends('layouts.base')
@section('titulo', 'Nuevo Docente')
@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-person-plus me-2"></i>Nuevo Docente</h1>
    <p class="page-subtitle">Registrar un docente para el CUP</p>
  </div>
  <a href="{{ route('docentes.index') }}" class="btn btn-outline-secondary">
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
    <form action="{{ route('docentes.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <h5 class="mb-3" style="color:var(--cup-primary-light);font-weight:600;">
        <i class="bi bi-clipboard me-2"></i>Datos Personales
      </h5>

      <div class="row g-3 mb-3">
        <div class="col-md-4">
          <label class="form-label">CI <span class="text-danger">*</span></label>
          <input type="text" name="ci" class="form-control" value="{{ old('ci') }}" required maxlength="20" placeholder="ej: 1234567">
        </div>
        <div class="col-md-8">
          <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
          <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required maxlength="200" placeholder="ej: Juan Carlos Pérez López">
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
        <input type="text" name="direccion" class="form-control" value="{{ old('direccion') }}" maxlength="255" placeholder="ej: Av. Cañoto #123, Santa Cruz">
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label">Teléfono</label>
          <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}" maxlength="20" placeholder="ej: 70000000">
        </div>
        <div class="col-md-6">
          <label class="form-label">Correo electrónico</label>
          <input type="email" name="correo" class="form-control" value="{{ old('correo') }}" maxlength="150" placeholder="ej: docente@uagrm.edu.bo">
        </div>
      </div>

      <hr>

      <h5 class="mb-3" style="color:var(--cup-primary-light);font-weight:600;">
        <i class="bi bi-mortarboard me-2"></i>Datos Académicos
      </h5>

      <div class="row g-3 mb-3">
        <div class="col-md-5">
          <label class="form-label">Profesión</label>
          <select name="profesion_id" class="form-select">
            <option value="">— Sin especificar —</option>
            @foreach($profesiones as $prof)
              <option value="{{ $prof->id }}" {{ old('profesion_id') == $prof->id ? 'selected' : '' }}>
                {{ $prof->nombre }} {{ $prof->nivel_jerarquico ? "({$prof->nivel_jerarquico})" : '' }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- ▼ CAMPO NUEVO: Materia que dicta --}}
        <div class="col-md-4">
          <label class="form-label fw-semibold">
            Materia que dicta <span class="text-danger">*</span>
          </label>
          <select name="materia" class="form-select @error('materia') is-invalid @enderror" required>
            <option value="">— Seleccionar materia —</option>
            @foreach($materias as $mat)
              <option value="{{ $mat->sigla }}" {{ old('materia') === $mat->sigla ? 'selected' : '' }}>
                {{ $mat->sigla }} — {{ $mat->nombre }}
              </option>
            @endforeach
          </select>
          @error('materia')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <small class="text-muted">Determina qué grupos puede recibir este docente</small>
        </div>

        <div class="col-md-3">
          <label class="form-label">Años de experiencia <span class="text-danger">*</span></label>
          <input type="number" name="anios_experiencia" class="form-control" value="{{ old('anios_experiencia', 0) }}" min="0" max="50" required>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label">Certificado docente <small class="text-muted">(PDF/JPG/PNG, máx 5MB)</small></label>
          <input type="file" name="certif_docente" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        </div>
        <div class="col-md-6">
          <label class="form-label">Certificado profesional <small class="text-muted">(PDF/JPG/PNG, máx 5MB)</small></label>
          <input type="file" name="certif_profesional" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        </div>
      </div>

      <hr>
      <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('docentes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-cup-primary">
          <i class="bi bi-check-circle me-1"></i> Guardar Docente
        </button>
      </div>
    </form>
  </div>
</div>

@endsection