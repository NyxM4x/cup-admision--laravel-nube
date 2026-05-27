@extends('layouts.base')
@section('titulo', 'Gestión de Roles')

@section('contenido')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><i class="bi bi-person-badge me-2"></i>Gestión de Roles</h4>
    <a href="{{ route('roles.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Rol
    </a>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('roles.index') }}" class="card border-0 shadow-sm p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-6">
            <label class="form-label small text-muted">Buscar</label>
            <input type="text" name="q" value="{{ $q }}" placeholder="Nombre o descripción..."
                   class="form-control form-control-sm">
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted">Estado</label>
            <select name="estado" class="form-select form-select-sm">
                <option value="activos"   @selected($estado === 'activos')>Activos</option>
                <option value="inactivos" @selected($estado === 'inactivos')>Inactivos</option>
                <option value="todos"     @selected($estado === 'todos')>Todos</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-secondary btn-sm w-100">
                <i class="bi bi-search me-1"></i>Filtrar
            </button>
        </div>
    </div>
</form>

{{-- Tabla --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover table-bordered mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th class="text-center"># Usuarios</th>
                    <th class="text-center"># Permisos</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $rol)
                    @php $esSistema = $rol->nombre === 'Administrador'; @endphp
                    <tr>
                        <td class="fw-semibold">{{ $rol->nombre }}</td>
                        <td>{{ $rol->descripcion ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $rol->usuarios_count }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info text-dark">{{ $rol->permisos_count }}</span>
                        </td>
                        <td class="text-center">
                            @if ($rol->activo)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-danger">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('roles.edit', $rol->id) }}"
                               class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            @if ($rol->activo)
                                @if ($esSistema)
                                    <button type="button" disabled
                                            class="btn btn-sm btn-secondary"
                                            title="Rol del sistema, no se puede inactivar">
                                        <i class="bi bi-lock"></i> Protegido
                                    </button>
                                @else
                                    <form method="POST" action="{{ route('roles.destroy', $rol->id) }}"
                                          class="d-inline"
                                          onsubmit="return confirm('¿Inactivar el rol {{ $rol->nombre }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-slash-circle"></i> Inactivar
                                        </button>
                                    </form>
                                @endif
                            @else
                                <form method="POST" action="{{ route('roles.reactivar', $rol->id) }}"
                                      class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="bi bi-check-circle"></i> Reactivar
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No se encontraron roles.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $roles->withQueryString()->links() }}
</div>
@endsection