@extends('layouts.base')

@section('titulo', 'Catálogo de Permisos')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-key-fill me-2"></i>Catálogo de Permisos del Sistema</h1>
    <p class="page-subtitle">Permisos definidos por la aplicación (solo lectura)</p>
  </div>
  <a href="{{ route('permisos.matriz') }}" class="btn btn-cup-primary">
    <i class="bi bi-grid-3x3 me-1"></i> Ver Matriz Rol-Permiso
  </a>
</div>

<div class="alert alert-info border-0" style="border-radius:8px">
  <i class="bi bi-info-circle me-2"></i>
  Los permisos del sistema están definidos por el código de la aplicación y no se crean dinámicamente.
  Para asignarlos a un rol, vaya a <a href="{{ route('roles.index') }}" class="fw-semibold">Gestión de Roles</a>.
</div>

<div class="panel-cup mb-3">
  <div class="panel-cup-header">
    <strong><i class="bi bi-funnel me-1"></i> Filtros</strong>
    <a href="{{ route('permisos.index') }}" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-x-circle me-1"></i> Limpiar
    </a>
  </div>
  <div class="panel-cup-body">
    <form method="GET" action="{{ route('permisos.index') }}" class="row g-3">
      <div class="col-md-8">
        <label class="form-label small text-muted">Buscar</label>
        <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Buscar por código o descripción...">
      </div>
      <div class="col-md-2">
        <label class="form-label small text-muted">Módulo</label>
        <select name="modulo" class="form-select" onchange="this.form.submit()">
          <option value="">Todos</option>
          @foreach($modulosDisponibles as $mod)
            <option value="{{ $mod }}" {{ $modulo === $mod ? 'selected' : '' }}>{{ $mod }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-cup-primary w-100">
          <i class="bi bi-search me-1"></i> Filtrar
        </button>
      </div>
    </form>
  </div>
</div>

@forelse($permisos as $modulo => $permisosModulo)
  <div class="panel-cup mb-3">
    <div class="panel-cup-header">
      <strong style="color:var(--cup-primary)">{{ $modulo }}
        <span class="text-muted fw-normal">({{ $permisosModulo->count() }})</span>
      </strong>
    </div>
    <div class="panel-cup-body p-0">
      <div class="table-responsive">
      <table class="table-cup table mb-0">
        <thead>
          <tr>
            <th>Código</th>
            <th>Descripción</th>
            <th class="text-center"># Roles con este permiso</th>
          </tr>
        </thead>
        <tbody>
          @foreach($permisosModulo as $permiso)
            <tr>
              <td><strong>{{ $permiso->codigo }}</strong></td>
              <td>{{ $permiso->descripcion }}</td>
              <td class="text-center"><span class="badge-cup badge-modulo">{{ $permiso->roles_count }}</span></td>
            </tr>
          @endforeach
        </tbody>
      </table>
      </div>
    </div>
  </div>
@empty
  <div class="panel-cup">
    <div class="panel-cup-body text-center text-muted">No se encontraron permisos.</div>
  </div>
@endforelse

@endsection
