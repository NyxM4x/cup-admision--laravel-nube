@extends('layouts.base')

@section('titulo', 'Bitácora del Sistema')

@section('contenido')

<div class="page-header mb-4">
  <h1><i class="bi bi-journal-text me-2"></i>Bitácora del Sistema</h1>
  <p class="page-subtitle">Registro inalterable de todas las acciones del sistema</p>
</div>

{{-- TARJETAS KPI --}}
<div class="row g-3 mb-4">
  <div class="col-md-3 col-sm-6">
    <div class="kpi-card kpi-primary h-100">
      <div class="kpi-icon"><i class="bi bi-list-ul"></i></div>
      <div class="kpi-value">{{ $estadisticas['total'] ?? 0 }}</div>
      <div class="kpi-label">Total Registros</div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="kpi-card kpi-success h-100">
      <div class="kpi-icon"><i class="bi bi-calendar-day"></i></div>
      <div class="kpi-value">{{ $estadisticas['hoy'] ?? 0 }}</div>
      <div class="kpi-label">Registros Hoy</div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="kpi-card kpi-warning h-100">
      <div class="kpi-icon"><i class="bi bi-clock-history"></i></div>
      <div class="kpi-value">{{ $estadisticas['ultimas_24h'] ?? 0 }}</div>
      <div class="kpi-label">Últimas 24h</div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="kpi-card kpi-danger h-100">
      <div class="kpi-icon"><i class="bi bi-exclamation-triangle"></i></div>
      <div class="kpi-value">{{ $estadisticas['eventos_criticos_24h'] ?? 0 }}</div>
      <div class="kpi-label">Críticos 24h</div>
    </div>
  </div>
</div>

{{-- FILTROS --}}
<div class="panel-cup mb-3">
  <div class="panel-cup-header">
    <strong><i class="bi bi-funnel me-1"></i> Filtros</strong>
    <a href="{{ route('bitacora.index') }}" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-x-circle me-1"></i> Limpiar
    </a>
  </div>
  <div class="panel-cup-body">
    <form method="GET" action="{{ route('bitacora.index') }}" class="row g-3">
      <div class="col-md-4">
        <label class="form-label small text-muted">Buscar en descripción</label>
        <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}" class="form-control" placeholder="Buscar...">
      </div>
      <div class="col-md-4">
        <label class="form-label small text-muted">Usuario</label>
        <select name="user_id" class="form-select" onchange="this.form.submit()">
          <option value="">Todos los usuarios</option>
          @foreach(($opciones['usuarios'] ?? []) as $u)
            <option value="{{ $u->id }}" {{ ($filtros['user_id'] ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label small text-muted">Módulo</label>
        <select name="modulo" class="form-select" onchange="this.form.submit()">
          <option value="">Todos los módulos</option>
          @foreach(($opciones['modulos'] ?? []) as $m)
            <option value="{{ $m }}" {{ ($filtros['modulo'] ?? '') === $m ? 'selected' : '' }}>{{ $m }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label small text-muted">Acción</label>
        <select name="accion" class="form-select" onchange="this.form.submit()">
          <option value="">Todas las acciones</option>
          @foreach(($opciones['acciones'] ?? []) as $a)
            <option value="{{ $a }}" {{ ($filtros['accion'] ?? '') === $a ? 'selected' : '' }}>{{ $a }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small text-muted">Desde</label>
        <input type="date" name="fecha_desde" value="{{ $filtros['fecha_desde'] ?? '' }}" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label small text-muted">Hasta</label>
        <input type="date" name="fecha_hasta" value="{{ $filtros['fecha_hasta'] ?? '' }}" class="form-control">
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-cup-primary w-100">
          <i class="bi bi-search me-1"></i> Filtrar
        </button>
      </div>
    </form>
  </div>
</div>

{{-- TABLA --}}
<div class="panel-cup">
  <div class="panel-cup-body p-0">
    <div class="table-responsive">
      <table class="table-cup table mb-0">
        <thead>
          <tr>
            <th style="width:60px;">#</th>
            <th style="width:160px;">Fecha/Hora</th>
            <th>Usuario</th>
            <th>Módulo</th>
            <th>Acción</th>
            <th>Descripción</th>
            <th style="width:120px;">IP</th>
            <th class="text-end" style="width:80px;">Detalle</th>
          </tr>
        </thead>
        <tbody>
          @forelse($registros as $r)
            @php
              $accion = $r->accion;
              $verdes = ['LOGIN_OK','USUARIO_CREADO','ROL_CREADO','AULA_CREADA','PERIODO_CREADO',
                         'CARRERA_CREADA','MATERIA_CREADA','DOCENTE_CREADO','POSTULANTE_CREADO',
                         'USUARIO_REACTIVADO','ROL_REACTIVADO','AULA_REACTIVADA','CREAR','ACTIVAR'];
              $rojos = ['LOGIN_FAIL','LOGIN_INACTIVO','ACCESO_DENEGADO','USUARIO_ELIMINADO',
                        'ROL_ELIMINADO','AULA_ELIMINADA','ELIMINAR'];
              $amarillos = ['USUARIO_INACTIVADO','ROL_INACTIVADO','AULA_INACTIVADA','LOGOUT_OK',
                            'USUARIO_EDITADO','ROL_EDITADO','AULA_EDITADA','EDITAR','INACTIVAR'];
              if (in_array($accion, $verdes))         $clase = 'badge-activo';
              elseif (in_array($accion, $rojos))      $clase = 'badge-inactivo';
              elseif (in_array($accion, $amarillos))  $clase = 'badge-warning-cup';
              else                                    $clase = 'badge-modulo';
            @endphp
            <tr>
              <td><small class="text-muted">{{ $r->id }}</small></td>
              <td><small>{{ $r->created_at?->format('d/m/Y H:i:s') }}</small></td>
              <td>
                @if($r->user)
                  {{ $r->user->name }}
                @else
                  <em class="text-muted">— Sistema —</em>
                @endif
              </td>
              <td><span class="badge-cup badge-modulo">{{ $r->modulo }}</span></td>
              <td><span class="badge-cup {{ $clase }}">{{ $r->accion }}</span></td>
              <td><small>{{ Str::limit($r->descripcion, 70) }}</small></td>
              <td><small class="text-muted">{{ $r->ip ?? '—' }}</small></td>
              <td class="text-end">
                <a href="{{ route('bitacora.show', $r->id) }}" class="btn-action btn-action-view" title="Ver detalle">
                  <i class="bi bi-eye"></i>
                </a>
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center py-4 text-muted">
              No se encontraron registros con los filtros aplicados.
            </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($registros->hasPages())
      <div class="d-flex justify-content-center py-3 border-top">
        {{ $registros->links() }}
      </div>
    @endif

  </div>
</div>

@endsection

@push('styles')
<style>
  .badge-warning-cup {
    background: rgba(245,158,11,0.12);
    color: #b45309;
    border: 1px solid rgba(245,158,11,0.30);
  }
</style>
@endpush
