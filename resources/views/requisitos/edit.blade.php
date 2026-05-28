@extends('layouts.base')

@section('titulo', 'Editar Requisito')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-list-check me-2"></i>Editar Requisito</h1>
    <p class="page-subtitle">Modificar el requisito de inscripción</p>
  </div>
  <a href="{{ route('requisitos.index') }}" class="btn btn-outline-secondary">
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
    <form action="{{ route('requisitos.update', $requisito) }}" method="POST">
      @csrf @method('PUT')

      <div class="mb-3">
        <label class="form-label">Nombre del Requisito</label>
        <input type="text" name="nombre" class="form-control" value="{{ $requisito->nombre }}" required maxlength="150">
      </div>
      <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-control" rows="2">{{ $requisito->descripcion }}</textarea>
      </div>
      <div class="form-check mb-3">
        <input type="checkbox" name="obligatorio" value="1" class="form-check-input" id="obligatorio" {{ old('obligatorio', $requisito->obligatorio) ? 'checked' : '' }}>
        <label class="form-check-label" for="obligatorio"><strong>Es obligatorio</strong></label>
      </div>
      <div class="mb-3">
        <label class="form-label">Formatos aceptados</label>
        <select name="formato_aceptado" class="form-select" required>
          <option value="PDF,JPG,PNG" {{ $requisito->formato_aceptado == 'PDF,JPG,PNG' ? 'selected' : '' }}>PDF, JPG, PNG</option>
          <option value="PDF"         {{ $requisito->formato_aceptado == 'PDF'         ? 'selected' : '' }}>Solo PDF</option>
          <option value="JPG,PNG"     {{ $requisito->formato_aceptado == 'JPG,PNG'     ? 'selected' : '' }}>Solo imágenes (JPG, PNG)</option>
        </select>
      </div>
      <div class="mb-4">
        <label class="form-label">Tamaño máximo del archivo</label>
        <select name="tamanio_max_kb" class="form-select" required>
          <option value="1024"  {{ $requisito->tamanio_max_kb == 1024  ? 'selected' : '' }}>1 MB</option>
          <option value="2048"  {{ $requisito->tamanio_max_kb == 2048  ? 'selected' : '' }}>2 MB</option>
          <option value="5120"  {{ $requisito->tamanio_max_kb == 5120  ? 'selected' : '' }}>5 MB</option>
          <option value="10240" {{ $requisito->tamanio_max_kb == 10240 ? 'selected' : '' }}>10 MB</option>
        </select>
      </div>

      <hr>
      <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('requisitos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-cup-primary">
          <i class="bi bi-check-circle me-1"></i> Actualizar Requisito
        </button>
      </div>
    </form>
  </div>
</div>

@endsection
