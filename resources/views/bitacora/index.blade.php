@extends('layouts.base')
@section('titulo', 'Bitácora del Sistema')

@section('contenido')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><i class="bi bi-journal-text me-2"></i>Bitácora del Sistema
        <small class="text-muted fs-6 fw-normal ms-2">Registro inalterable de acciones</small>
    </h4>
</div>

{{-- Estadísticas --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <i class="bi bi-list-ul fs-2 text-primary"></i>
            <h4 class="mt-1 mb-0">{{ number_format($estadisticas['total']) }}</h4>
            <small class="text-muted">Total registros</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <i class="bi bi-calendar-day fs-2 text-success"></i>
            <h4 class="mt-1 mb-0">{{ number_format($estadisticas['hoy']) }}</h4>
            <small class="text-muted">Registros hoy</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <i class="bi bi-clock-history fs-2 text-warning"></i>
            <h4 class="mt-1 mb-0">{{ number_format($estadisticas['ultimas_24h']) }}</h4>
            <small class="text-muted">Últimas 24h</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <i class="bi bi-exclamation-triangle fs-2 {{ $estadisticas['eventos_criticos_24h'] > 0 ? 'text-danger' : 'text-secondary' }}"></i>
            <h4 class="mt-1 mb-0 {{ $estadisticas['eventos_criticos_24h'] > 0 ? 'text-danger' : '' }}">
                {{ number_format($estadisticas['eventos_criticos_24h']) }}
            </h4>
            <small class="text-muted">Críticos 24h</small>
        </div>
    </div>
</div>

{{-- Filtros --}}
<form method="GET" action="{{ route('bitacora.index') }}" class="card border-0 shadow-sm p-3 mb-3">
    <div class="row g-2">
        <div class="col-md-4">
            <label class="form-label small text-muted">Buscar en descripción</label>
            <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}"
                   placeholder="Buscar..." class="form-control form-control-sm">
        </div>
        <div class="col-md-4">
            <label class="form-label small text-muted">Usuario</label>
            <select name="user_id" class="form-select form-select-sm">
                <option value="">Todos los usuarios</option>
                @foreach ($opciones['usuarios'] as $u)
                    <option value="{{ $u->id }}" @selected(($filtros['user_id'] ?? '') == $u->id)>
                        {{ $u->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small text-muted">Módulo</label>
            <select name="modulo" class="form-select form-select-sm">
                <option value="">Todos los módulos</option>
                @foreach ($opciones['modulos'] as $mod)
                    <option value="{{ $mod }}" @selected(($filtros['modulo'] ?? '') === $mod)>{{ $mod }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small text-muted">Acción</label>
            <select name="accion" class="form-select form-select-sm">
                <option value="">Todas las acciones</option>
                @foreach ($opciones['acciones'] as $acc)
                    <option value="{{ $acc }}" @selected(($filtros['accion'] ?? '') === $acc)>{{ $acc }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted">Desde</label>
            <input type="date" name="fecha_desde" value="{{ $filtros['fecha_desde'] ?? '' }}"
                   class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted">Hasta</label>
            <input type="date" name="fecha_hasta" value="{{ $filtros['fecha_hasta'] ?? '' }}"
                   class="form-control form-control-sm">
        </div>
        <div class="col-md-4 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-secondary btn-sm">
                <i class="bi bi-search me-1"></i>Filtrar
            </button>
            <a href="{{ route('bitacora.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-x-lg me-1"></i>Limpiar
            </a>
        </div>
    </div>
</form>

{{-- Tabla --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover table-bordered table-sm mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Fecha/Hora</th>
                    <th>Usuario</th>
                    <th>Módulo</th>
                    <th>Acción</th>
                    <th>Descripción</th>
                    <th>IP</th>
                    <th class="text-center">Detalle</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($registros as $r)
                    @php
                        $accion = $r->accion;
                        if (preg_match('/(_OK|_CREADO|_REACTIVADO)$/', $accion) && $accion !== 'LOGOUT_OK') {
                            $badgeClass = 'bg-success';
                        } elseif (in_array($accion, ['LOGIN_FAIL', 'LOGIN_INACTIVO', 'ACCESO_DENEGADO'])) {
                            $badgeClass = 'bg-danger';
                        } elseif (in_array($accion, ['USUARIO_INACTIVADO', 'ROL_INACTIVADO', 'LOGOUT_OK'])) {
                            $badgeClass = 'bg-warning text-dark';
                        } else {
                            $badgeClass = 'bg-secondary';
                        }
                    @endphp
                    <tr>
                        <td class="text-muted small">{{ $r->id }}</td>
                        <td class="small text-nowrap">{{ $r->created_at?->format('d/m/Y H:i:s') }}</td>
                        <td class="small">{{ $r->user?->name ?? '— Sistema —' }}</td>
                        <td><span class="badge bg-primary">{{ $r->modulo }}</span></td>
                        <td><span class="badge {{ $badgeClass }}">{{ $r->accion }}</span></td>
                        <td class="small">{{ Str::limit($r->descripcion, 80) }}</td>
                        <td class="small text-muted">{{ $r->ip ?? '—' }}</td>
                        <td class="text-center">
                            <a href="{{ route('bitacora.show', $r->id) }}"
                               class="btn btn-sm btn-outline-primary py-0 px-2">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No se encontraron registros con los filtros aplicados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $registros->links() }}
</div>
@endsection