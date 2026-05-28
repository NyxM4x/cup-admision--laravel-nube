@extends('layouts.base')

@section('titulo', 'Nueva Aula')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-plus-circle me-2"></i>Nueva Aula</h1>
    <p class="page-subtitle">Registrar un aula nueva en el catálogo</p>
  </div>
  <a href="{{ route('aulas.index') }}" class="btn btn-outline-secondary">
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
    <form method="POST" action="{{ route('aulas.store') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Código <span class="text-danger">*</span></label>
        <input type="text" name="codigo" value="{{ old('codigo') }}" class="form-control" maxlength="20" required placeholder="Ej: A-101">
      </div>
      <div class="mb-3">
        <label class="form-label">Edificio <span class="text-danger">*</span></label>
        <input type="text" name="edificio" value="{{ old('edificio') }}" class="form-control" maxlength="50" required placeholder="Ej: Bloque A">
      </div>
      <div class="mb-3">
        <label class="form-label">Capacidad <span class="text-danger">*</span></label>
        <input type="number" name="capacidad" value="{{ old('capacidad') }}" class="form-control" min="1" max="500" required placeholder="Ej: 70">
        <small class="text-muted">Número de estudiantes que pueden ocupar el aula.</small>
      </div>
      <div class="mb-4">
        <label class="form-label">Equipamiento</label>
        <textarea name="equipamiento" class="form-control" rows="3" maxlength="1000" placeholder="Proyector, pizarra, etc.">{{ old('equipamiento') }}</textarea>
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-cup-primary">
          <i class="bi bi-check-circle me-1"></i> Guardar
        </button>
        <a href="{{ route('aulas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>

@endsection
