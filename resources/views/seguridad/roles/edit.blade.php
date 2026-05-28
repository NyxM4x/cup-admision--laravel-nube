@extends('layouts.base')

@section('titulo', 'Editar Rol')

@section('contenido')

@php
  $esAdmin = $rolModel->nombre === 'Administrador';
  $marcados = old('permisos', $permisosAsignados);
@endphp

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-pencil-square me-2"></i>Editar Rol: {{ $rolModel->nombre }}</h1>
    <p class="page-subtitle">Modificar datos y permisos del rol</p>
  </div>
  <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

@if($errors->any())
  <div class="alert alert-cup-danger">
    <i class="bi bi-exclamation-triangle me-2"></i>
    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
  </div>
@endif

@if($esAdmin)
  <div class="alert alert-warning border-0" style="border-radius:8px">
    <i class="bi bi-shield-fill-exclamation me-2"></i>
    El rol <strong>Administrador</strong> es del sistema y siempre conserva todos los permisos. Su nombre y permisos no se pueden modificar.
  </div>
@endif

<div class="panel-cup" style="max-width:900px">
  <div class="panel-cup-body">
    <form method="POST" action="{{ route('roles.update', $rolModel->id) }}">
      @csrf @method('PUT')

      <div class="mb-3">
        <label class="form-label">Nombre <span class="text-danger">*</span></label>
        <input type="text" name="nombre" value="{{ old('nombre', $rolModel->nombre) }}" class="form-control"
               required autofocus {{ $esAdmin ? 'readonly' : '' }}>
      </div>

      <div class="mb-4">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" rows="2" class="form-control">{{ old('descripcion', $rolModel->descripcion) }}</textarea>
      </div>

      <h5 class="mb-3" style="color:var(--cup-primary)">
        <i class="bi bi-key-fill me-1"></i> Permisos del rol
      </h5>

      @foreach($permisos as $modulo => $permisosModulo)
        <h6 class="text-uppercase small fw-bold text-muted border-bottom pb-1 mt-3 mb-2">{{ $modulo }}</h6>
        <div class="row g-2 mb-2">
          @foreach($permisosModulo as $permiso)
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="permisos[]" value="{{ $permiso->id }}"
                       id="perm{{ $permiso->id }}"
                       {{ ($esAdmin || in_array($permiso->id, $marcados)) ? 'checked' : '' }}
                       {{ $esAdmin ? 'disabled' : '' }}>
                <label class="form-check-label" for="perm{{ $permiso->id }}">
                  <strong>{{ $permiso->codigo }}</strong>: {{ $permiso->descripcion }}
                </label>
              </div>
            </div>
          @endforeach
        </div>
      @endforeach

      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-cup-primary">
          <i class="bi bi-check-circle me-1"></i> Actualizar
        </button>
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>

@endsection
