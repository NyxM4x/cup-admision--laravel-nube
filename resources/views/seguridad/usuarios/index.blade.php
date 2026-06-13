@extends('layouts.base')

@section('titulo', 'Gestión de Usuarios')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-people-fill me-2"></i>Gestión de Usuarios</h1>
    <p class="page-subtitle">Administración de cuentas de acceso al sistema</p>
  </div>
  <a href="{{ route('usuarios.create') }}" class="btn btn-cup-primary">
    <i class="bi bi-plus-circle me-1"></i> Nuevo Usuario
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
    <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-x-circle me-1"></i> Limpiar
    </a>
  </div>
  <div class="panel-cup-body">

    <form method="GET" action="{{ route('usuarios.index') }}" class="row g-3 mb-4">
      <div class="col-md-7">
        <label class="form-label small text-muted">Buscar</label>
        <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Nombre, email o CI...">
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
          <th>Email</th>
          <th>CI</th>
          <th>Rol</th>
          <th class="text-center">Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($usuarios as $u)
        <tr>
          <td><strong>{{ $u->name }}</strong></td>
          <td>{{ $u->email }}</td>
          <td>{{ $u->ci ?? '—' }}</td>
          <td>
            @if($u->rol)
              <span class="badge-cup badge-modulo">{{ $u->rol->nombre }}</span>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td class="text-center">
            @if($u->bloqueado_hasta && now()->lessThan($u->bloqueado_hasta))
              <span class="badge-cup badge-danger">Bloqueado</span>
            @elseif($u->activo)
              <span class="badge-cup badge-activo">Activo</span>
            @else
              <span class="badge-cup badge-inactivo">Inactivo</span>
            @endif
          </td>
          <td class="text-end">
            <a href="{{ route('usuarios.edit', $u->id) }}" class="btn-action btn-action-edit" title="Editar">
              <i class="bi bi-pencil"></i>
            </a>
	    @if($u->bloqueado_hasta && now()->lessThan($u->bloqueado_hasta))
              <form id="form-desbloquear-usuario-{{ $u->id }}"
                    action="{{ route('usuarios.reactivar', $u->id) }}" method="POST" style="display:inline">
                @csrf
                <button type="button" class="btn-action btn-action-success" title="Desbloquear"
                        onclick="cupConfirmar({
                          titulo: 'Desbloquear usuario',
                          mensaje: '¿Querés desbloquear al usuario {{ addslashes($u->name) }}?',
                          subtexto: 'Se le permitirá iniciar sesión nuevamente.',
                          textoBoton: 'Sí, desbloquear',
                          tipo: 'success',
                          formSelector: '#form-desbloquear-usuario-{{ $u->id }}'
                        })">
                  <i class="bi bi-unlock"></i>
                </button>
              </form>
            @elseif($u->activo)
              <form id="form-inactivar-usuario-{{ $u->id }}"
                    action="{{ route('usuarios.destroy', $u->id) }}" method="POST" style="display:inline">
                @csrf @method('DELETE')
                <button type="button" class="btn-action btn-action-danger" title="Inactivar"
                        onclick="cupConfirmar({
                          titulo: 'Inactivar usuario',
                          mensaje: '¿Querés inactivar al usuario {{ addslashes($u->name) }}?',
                          subtexto: 'No podrá iniciar sesión hasta que lo reactives.',
                          textoBoton: 'Sí, inactivar',
                          tipo: 'danger',
                          formSelector: '#form-inactivar-usuario-{{ $u->id }}'
                        })">
                  <i class="bi bi-archive"></i>
                </button>
              </form>
            @else
              <form id="form-reactivar-usuario-{{ $u->id }}"
                    action="{{ route('usuarios.reactivar', $u->id) }}" method="POST" style="display:inline">
                @csrf
                <button type="button" class="btn-action btn-action-success" title="Reactivar"
                        onclick="cupConfirmar({
                          titulo: 'Reactivar usuario',
                          mensaje: '¿Querés reactivar al usuario {{ addslashes($u->name) }}?',
                          textoBoton: 'Sí, reactivar',
                          tipo: 'success',
                          formSelector: '#form-reactivar-usuario-{{ $u->id }}'
                        })">
                  <i class="bi bi-arrow-counterclockwise"></i>
                </button>
              </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center py-4 text-muted">No se encontraron usuarios.</td></tr>
        @endforelse
      </tbody>
    </table>
    </div>

    @if($usuarios->hasPages())
      <div class="mt-3 d-flex justify-content-center">
        {{ $usuarios->withQueryString()->links('pagination::bootstrap-5') }}
      </div>
    @endif

  </div>
</div>

@endsection


