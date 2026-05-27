@extends('layouts.base')
@section('titulo', 'Gestión de Usuarios')

@section('contenido')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><i class="bi bi-people-fill me-2"></i>Gestión de Usuarios</h4>
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Usuario
    </a>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('usuarios.index') }}" class="card border-0 shadow-sm p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-6">
            <label class="form-label small text-muted">Buscar</label>
            <input type="text" name="q" value="{{ $q }}" placeholder="Nombre, email o CI..."
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
                    <th>Email</th>
                    <th>CI</th>
                    <th>Rol</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usuarios as $u)
                    <tr>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->ci ?? '—' }}</td>
                        <td>
                            @if($u->rol)
                                <span class="badge bg-primary">{{ $u->rol->nombre }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($u->activo)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-danger">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('usuarios.edit', $u->id) }}"
                               class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                            @if ($u->activo)
                                <form method="POST" action="{{ route('usuarios.destroy', $u->id) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('¿Inactivar al usuario {{ $u->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-slash-circle"></i> Inactivar
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('usuarios.reactivar', $u->id) }}"
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
                            No se encontraron usuarios.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Paginación --}}
<div class="mt-3">
    {{ $usuarios->withQueryString()->links() }}
</div>
@endsection