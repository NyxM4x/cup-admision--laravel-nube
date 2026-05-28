@extends('layouts.base')

@section('titulo', 'Gestión de Aulas')

@section('contenido')

{{-- PAGE HEADER --}}
<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-door-open me-2"></i>Gestión de Aulas</h1>
    <p class="page-subtitle">Catálogo de aulas disponibles para el Curso Preuniversitario</p>
  </div>
  <a href="{{ route('aulas.create') }}" class="btn btn-cup-primary">
    <i class="bi bi-plus-circle me-1"></i> Nueva Aula
  </a>
</div>

{{-- ALERTAS --}}
@if(session('success'))
  <div class="alert alert-cup-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif
@if($errors->any())
  <div class="alert alert-cup-danger">
    <i class="bi bi-exclamation-triangle me-2"></i>
    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
  </div>
@endif

{{-- TARJETAS KPI --}}
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="kpi-card kpi-primary">
      <div class="kpi-icon"><i class="bi bi-door-open-fill"></i></div>
      <div class="kpi-value">{{ $estadisticas['total_activas'] ?? 0 }}</div>
      <div class="kpi-label">Aulas Activas</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="kpi-card kpi-success">
      <div class="kpi-icon"><i class="bi bi-people-fill"></i></div>
      <div class="kpi-value">{{ $estadisticas['capacidad_total'] ?? 0 }}</div>
      <div class="kpi-label">Capacidad Total (estudiantes)</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="kpi-card kpi-accent">
      <div class="kpi-icon"><i class="bi bi-building"></i></div>
      <div class="kpi-value">{{ $estadisticas['edificios'] ?? 0 }}</div>
      <div class="kpi-label">Edificios</div>
    </div>
  </div>
</div>

{{-- FILTROS + TABLA EN UN PANEL --}}
<div class="panel-cup">
  <div class="panel-cup-header">
    <strong><i class="bi bi-funnel me-1"></i> Filtros</strong>
    <a href="{{ route('aulas.index') }}" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-x-circle me-1"></i> Limpiar
    </a>
  </div>
  <div class="panel-cup-body">

    <form method="GET" action="{{ route('aulas.index') }}" class="row g-3 mb-4">
      <div class="col-md-5">
        <label class="form-label small text-muted">Buscar</label>
        <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control"
               placeholder="Buscar por código, edificio o equipamiento...">
      </div>
      <div class="col-md-3">
        <label class="form-label small text-muted">Edificio</label>
        <select name="edificio" class="form-select" onchange="this.form.submit()">
          <option value="">Todos los edificios</option>
          @foreach(($edificios ?? []) as $ed)
            <option value="{{ $ed }}" {{ ($edificio ?? '') === $ed ? 'selected' : '' }}>{{ $ed }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label small text-muted">Estado</label>
        <select name="estado" class="form-select" onchange="this.form.submit()">
          <option value="activos"   {{ ($estado ?? '') === 'activos'   ? 'selected' : '' }}>Activas</option>
          <option value="inactivos" {{ ($estado ?? '') === 'inactivos' ? 'selected' : '' }}>Inactivas</option>
          <option value="todos"     {{ ($estado ?? '') === 'todos'     ? 'selected' : '' }}>Todas</option>
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
          <th>Código</th>
          <th>Edificio</th>
          <th>Capacidad</th>
          <th>Equipamiento</th>
          <th>Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($aulas as $aula)
        <tr>
          <td><strong>{{ $aula->codigo }}</strong></td>
          <td>{{ $aula->edificio }}</td>
          <td>{{ $aula->capacidad }} <small class="text-muted">estudiantes</small></td>
          <td><small>{{ Str::limit($aula->equipamiento, 60) }}</small></td>
          <td>
            @if($aula->activo)
              <span class="badge-cup badge-activo">Activa</span>
            @else
              <span class="badge-cup badge-inactivo">Inactiva</span>
            @endif
          </td>
          <td class="text-end">
            <a href="{{ route('aulas.edit', $aula->id) }}"
               class="btn-action btn-action-edit" title="Editar">
              <i class="bi bi-pencil"></i>
            </a>
            @if($aula->activo)
              <form action="{{ route('aulas.destroy', $aula->id) }}" method="POST"
                    style="display:inline" onsubmit="return confirm('¿Inactivar esta aula?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-action btn-action-danger" title="Inactivar">
                  <i class="bi bi-archive"></i>
                </button>
              </form>
            @else
              <form action="{{ route('aulas.reactivar', $aula->id) }}" method="POST"
                    style="display:inline">
                @csrf
                <button type="submit" class="btn-action btn-action-success" title="Reactivar">
                  <i class="bi bi-arrow-counterclockwise"></i>
                </button>
              </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center py-4 text-muted">No se encontraron aulas con esos filtros.</td></tr>
        @endforelse
      </tbody>
    </table>
    </div>

    @if($aulas->hasPages())
      <div class="mt-3 d-flex justify-content-center">
        {{ $aulas->links() }}
      </div>
    @endif

  </div>
</div>

@endsection
