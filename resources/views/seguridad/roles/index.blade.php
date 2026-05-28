@extends('layouts.base')

@section('titulo', 'Gestión de Roles')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-shield-lock me-2"></i>Gestión de Roles</h1>
    <p class="page-subtitle">Roles del sistema y sus permisos asignados</p>
  </div>
  <a href="{{ route('roles.create') }}" class="btn btn-cup-primary">
    <i class="bi bi-plus-circle me-1"></i> Nuevo Rol
  </a>
</div>

@if($errors->any())
  <div class="alert alert-cup-danger">
    <i class="bi bi-exclamation-triangle me-2"></i>
    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
  </div>
@endif

<div class="panel-cup">
  <div class="panel-cup-header">
    <strong><i class="bi bi-funnel me-1"></i> Filtros</strong>
    <a href="{{ route('roles.index') }}" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-x-circle me-1"></i> Limpiar
    </a>
  </div>
  <div class="panel-cup-body">

    <form method="GET" action="{{ route('roles.index') }}" class="row g-3 mb-4">
      <div class="col-md-7">
        <label class="form-label small text-muted">Buscar</label>
        <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Nombre o descripción...">
      </div>
      <div class="col-md-3">
        <label class="form-label small text-muted">Estado</label>
        <select name="estado" class="form-select" onchange="this.form.submit()">
          <option value="activos"   {{ $estado === 'activos'   ? 'selected' : '' }}>Activos</option>
          <option value="inactivos" {{ $estado === 'inactivos' ? 'selected' : '' }}>Inactivos</option>
          <option value="todos"     {{ $estado === 'todos'     ? 'selected' : '' }}>Todos</option>
        </select>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-cup-primary w-100">
          <i class="bi bi-search me-1"></i> Filtrar
        </button>
      </div>
    </form>

    <div class="table-responsive">
    <table class="table-cup table mb-0">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Descripción</th>
          <th class="text-center"># Usuarios</th>
          <th class="text-center"># Permisos</th>
          <th class="text-center">Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($roles as $rol)
          @php $esSistema = $rol->nombre === 'Administrador'; @endphp
          <tr>
            <td><strong>{{ $rol->nombre }}</strong></td>
            <td>{{ $rol->descripcion ?? '—' }}</td>
            <td class="text-center"><span class="badge-cup badge-modulo">{{ $rol->usuarios_count }}</span></td>
            <td class="text-center"><span class="badge-cup badge-modulo">{{ $rol->permisos_count }}</span></td>
            <td class="text-center">
              @if($rol->activo)
                <span class="badge-cup badge-activo">Activo</span>
              @else
                <span class="badge-cup badge-inactivo">Inactivo</span>
              @endif
            </td>
            <td class="text-end">
              <a href="{{ route('roles.edit', $rol->id) }}" class="btn-action btn-action-edit" title="Editar">
                <i class="bi bi-pencil"></i>
              </a>
              @if($rol->activo)
                @if($esSistema)
                  <button type="button" class="btn-action btn-action-danger" disabled
                          title="Rol del sistema, no se puede inactivar"
                          style="opacity:.45;cursor:not-allowed">
                    <i class="bi bi-lock"></i>
                  </button>
                @else
                  <form action="{{ route('roles.destroy', $rol->id) }}" method="POST" style="display:inline"
                        onsubmit="return confirm('¿Inactivar el rol {{ $rol->nombre }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-action btn-action-danger" title="Inactivar">
                      <i class="bi bi-archive"></i>
                    </button>
                  </form>
                @endif
              @else
                <form action="{{ route('roles.reactivar', $rol->id) }}" method="POST" style="display:inline">
                  @csrf
                  <button type="submit" class="btn-action btn-action-success" title="Reactivar">
                    <i class="bi bi-arrow-counterclockwise"></i>
                  </button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center py-4 text-muted">No se encontraron roles.</td></tr>
        @endforelse
      </tbody>
    </table>
    </div>

    @if($roles->hasPages())
      <div class="mt-3 d-flex justify-content-center">
        {{ $roles->withQueryString()->links() }}
      </div>
    @endif

  </div>
</div>

@endsection
