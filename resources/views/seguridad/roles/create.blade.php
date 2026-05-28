@extends('layouts.base')

@section('titulo', 'Nuevo Rol')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-plus-circle me-2"></i>Nuevo Rol</h1>
    <p class="page-subtitle">Crear un rol y asignarle permisos</p>
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

<div class="panel-cup" style="max-width:900px">
  <div class="panel-cup-body">
    @php $permisosViejos = old('permisos', []); @endphp
    <form method="POST" action="{{ route('roles.store') }}">
      @csrf

      <div class="mb-3">
        <label class="form-label">Nombre <span class="text-danger">*</span></label>
        <input type="text" name="nombre" value="{{ old('nombre') }}" class="form-control" required autofocus>
      </div>

      <div class="mb-4">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" rows="2" class="form-control">{{ old('descripcion') }}</textarea>
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
                       id="perm{{ $permiso->id }}" {{ in_array($permiso->id, $permisosViejos) ? 'checked' : '' }}>
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
          <i class="bi bi-check-circle me-1"></i> Guardar
        </button>
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>

@endsection
