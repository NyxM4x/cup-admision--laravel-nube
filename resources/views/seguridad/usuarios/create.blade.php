@extends('layouts.base')

@section('titulo', 'Nuevo Usuario')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-person-plus me-2"></i>Nuevo Usuario</h1>
    <p class="page-subtitle">Registrar una cuenta de acceso al sistema</p>
  </div>
  <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
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
    <form method="POST" action="{{ route('usuarios.store') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Nombre <span class="text-danger">*</span></label>
        <input type="text" name="name" value="{{ old('name') }}" class="form-control" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">CI <small class="text-muted">(opcional)</small></label>
        <input type="text" name="ci" value="{{ old('ci') }}" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Teléfono <small class="text-muted">(opcional)</small></label>
        <input type="text" name="telefono" value="{{ old('telefono') }}" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Rol <span class="text-danger">*</span></label>
        <select name="rol_id" class="form-select" required>
          <option value="">— Seleccionar rol —</option>
          @foreach($roles as $rol)
            <option value="{{ $rol->id }}" {{ old('rol_id') == $rol->id ? 'selected' : '' }}>{{ $rol->nombre }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Contraseña <span class="text-danger">*</span></label>
        <input type="password" name="password" class="form-control" required autocomplete="new-password">
      </div>
      <div class="mb-4">
        <label class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
        <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-cup-primary">
          <i class="bi bi-check-circle me-1"></i> Guardar
        </button>
        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>

@endsection
