@extends('layouts.base')

@section('titulo', 'Editar Aula')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-pencil-square me-2"></i>Editar Aula: {{ $aulaModel->codigo }}</h1>
    <p class="page-subtitle">Modificar los datos del aula</p>
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
    <form method="POST" action="{{ route('aulas.update', $aulaModel->id) }}">
      @csrf @method('PUT')
      <div class="mb-3">
        <label class="form-label">Código <span class="text-danger">*</span></label>
        <input type="text" name="codigo" value="{{ old('codigo', $aulaModel->codigo) }}" class="form-control" maxlength="20" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Edificio <span class="text-danger">*</span></label>
        <input type="text" name="edificio" value="{{ old('edificio', $aulaModel->edificio) }}" class="form-control" maxlength="50" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Capacidad <span class="text-danger">*</span></label>
        <input type="number" name="capacidad" value="{{ old('capacidad', $aulaModel->capacidad) }}" class="form-control" min="1" max="500" required>
      </div>
      <div class="mb-4">
        <label class="form-label">Equipamiento</label>
        <textarea name="equipamiento" class="form-control" rows="3" maxlength="1000">{{ old('equipamiento', $aulaModel->equipamiento) }}</textarea>
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-cup-primary">
          <i class="bi bi-check-circle me-1"></i> Actualizar
        </button>
        <a href="{{ route('aulas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>

@endsection
