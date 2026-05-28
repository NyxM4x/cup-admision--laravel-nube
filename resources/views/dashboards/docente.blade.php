@extends('layouts.base')

@section('titulo', 'Dashboard Docente')

@section('contenido')

<div class="page-header mb-4">
  <h1><i class="bi bi-person-workspace me-2"></i>Panel del Docente</h1>
  <p class="page-subtitle">
    Bienvenido/a, <strong>{{ Auth::user()->name }}</strong>
    — Rol: <strong>{{ Auth::user()->rol->nombre ?? 'Sin rol' }}</strong>
  </p>
</div>

<div class="alert alert-warning border-0 d-flex align-items-start gap-2" style="border-radius:8px">
  <i class="bi bi-info-circle mt-1"></i>
  <div>
    <strong>Funcionalidad en desarrollo:</strong> el registro de notas y la consulta de
    grupos estarán disponibles en próximas iteraciones.
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-6 col-lg-3">
    <div class="kpi-card kpi-primary h-100">
      <div class="kpi-icon"><i class="bi bi-mortarboard"></i></div>
      <div class="kpi-value">0</div>
      <div class="kpi-label">Grupos asignados</div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="kpi-card kpi-success h-100">
      <div class="kpi-icon"><i class="bi bi-pencil-square"></i></div>
      <div class="kpi-value">0</div>
      <div class="kpi-label">Notas registradas</div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="kpi-card kpi-warning h-100">
      <div class="kpi-icon"><i class="bi bi-calendar3"></i></div>
      <div class="kpi-value">0</div>
      <div class="kpi-label">Próximos exámenes</div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="kpi-card kpi-accent h-100">
      <div class="kpi-icon"><i class="bi bi-book"></i></div>
      <div class="kpi-value">0</div>
      <div class="kpi-label">Materias dictadas</div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="panel-cup h-100">
      <div class="panel-cup-header">
        <strong><i class="bi bi-rocket-takeoff me-1"></i> Accesos rápidos</strong>
      </div>
      <div class="panel-cup-body">
        <div class="row g-2">
          @if(Route::has('profile.edit'))
          <div class="col-md-6">
            <a href="{{ route('profile.edit') }}" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-reset rounded" style="background:#f8f9fb;border:1px solid var(--cup-border);">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(30,95,168,0.10);display:flex;align-items:center;justify-content:center;color:var(--cup-primary-light);"><i class="bi bi-person-gear fs-4"></i></div>
              <div><div style="font-weight:600;">Ver mi perfil</div><small class="text-muted">Datos de la cuenta</small></div>
            </a>
          </div>
          @endif
          <div class="col-md-6">
            <a href="{{ route('aulas.index') }}" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-reset rounded" style="background:#f8f9fb;border:1px solid var(--cup-border);">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(44,123,229,0.10);display:flex;align-items:center;justify-content:center;color:var(--cup-accent);"><i class="bi bi-door-open fs-4"></i></div>
              <div><div style="font-weight:600;">Aulas</div><small class="text-muted">Catálogo de aulas</small></div>
            </a>
          </div>
          @if(Auth::user() && Auth::user()->tienePermiso('bitacora.ver'))
          <div class="col-md-6">
            <a href="{{ route('bitacora.index') }}" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-reset rounded" style="background:#f8f9fb;border:1px solid var(--cup-border);">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(245,158,11,0.10);display:flex;align-items:center;justify-content:center;color:var(--cup-warning);"><i class="bi bi-journal-text fs-4"></i></div>
              <div><div style="font-weight:600;">Bitácora</div><small class="text-muted">Registro de acciones</small></div>
            </a>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="panel-cup h-100">
      <div class="panel-cup-header">
        <strong><i class="bi bi-info-circle me-1"></i> Información</strong>
      </div>
      <div class="panel-cup-body">
        <p class="text-muted mb-0" style="font-size:0.92rem;line-height:1.6;">
          Como <strong>Docente</strong>, podrás registrar notas de exámenes y consultar
          grupos asignados a tus materias del CUP.
        </p>
      </div>
    </div>
  </div>
</div>

@endsection
