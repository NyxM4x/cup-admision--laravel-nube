@extends('layouts.base')

@section('titulo', 'Dashboard Auditor')

@section('contenido')

@php
  try {
    $totalBitacora = \App\Models\Bitacora::count();
    $eventosHoy = \App\Models\Bitacora::whereDate('created_at', today())->count();
    $loginsFallidos = \App\Models\Bitacora::where('accion', 'LOGIN_FAIL')->count();
    $accesosDenegados = \App\Models\Bitacora::where('accion', 'ACCESO_DENEGADO')->count();
  } catch (\Exception $e) {
    $totalBitacora = 0; $eventosHoy = 0; $loginsFallidos = 0; $accesosDenegados = 0;
  }
@endphp

<div class="page-header mb-4">
  <h1><i class="bi bi-binoculars-fill me-2"></i>Panel del Auditor</h1>
  <p class="page-subtitle">
    Bienvenido/a, <strong>{{ Auth::user()->name }}</strong>
    — Rol: <strong>{{ Auth::user()->rol->nombre ?? 'Sin rol' }}</strong>
  </p>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-6 col-lg-3">
    <a href="{{ route('bitacora.index') }}" class="text-decoration-none text-reset">
      <div class="kpi-card kpi-primary h-100">
        <div class="kpi-icon"><i class="bi bi-journal-text"></i></div>
        <div class="kpi-value">{{ $totalBitacora }}</div>
        <div class="kpi-label">Total registros bitácora</div>
      </div>
    </a>
  </div>
  <div class="col-md-6 col-lg-3">
    <a href="{{ route('bitacora.index') }}" class="text-decoration-none text-reset">
      <div class="kpi-card kpi-success h-100">
        <div class="kpi-icon"><i class="bi bi-calendar-day"></i></div>
        <div class="kpi-value">{{ $eventosHoy }}</div>
        <div class="kpi-label">Eventos hoy</div>
      </div>
    </a>
  </div>
  <div class="col-md-6 col-lg-3">
    <a href="{{ route('bitacora.index') }}?accion=LOGIN_FAIL" class="text-decoration-none text-reset">
      <div class="kpi-card kpi-danger h-100">
        <div class="kpi-icon"><i class="bi bi-shield-exclamation"></i></div>
        <div class="kpi-value">{{ $loginsFallidos }}</div>
        <div class="kpi-label">Logins fallidos</div>
      </div>
    </a>
  </div>
  <div class="col-md-6 col-lg-3">
    <a href="{{ route('bitacora.index') }}?accion=ACCESO_DENEGADO" class="text-decoration-none text-reset">
      <div class="kpi-card kpi-warning h-100">
        <div class="kpi-icon"><i class="bi bi-x-octagon"></i></div>
        <div class="kpi-value">{{ $accesosDenegados }}</div>
        <div class="kpi-label">Accesos denegados</div>
      </div>
    </a>
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
          <div class="col-md-6">
            <a href="{{ route('bitacora.index') }}" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-reset rounded" style="background:#f8f9fb;border:1px solid var(--cup-border);">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(30,95,168,0.10);display:flex;align-items:center;justify-content:center;color:var(--cup-primary-light);"><i class="bi bi-journal-text fs-4"></i></div>
              <div><div style="font-weight:600;">Ver bitácora completa</div><small class="text-muted">Todos los registros</small></div>
            </a>
          </div>
          <div class="col-md-6">
            <a href="{{ route('bitacora.index') }}?accion=LOGIN_FAIL" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-reset rounded" style="background:#f8f9fb;border:1px solid var(--cup-border);">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(220,38,38,0.08);display:flex;align-items:center;justify-content:center;color:var(--cup-danger);"><i class="bi bi-shield-exclamation fs-4"></i></div>
              <div><div style="font-weight:600;">Filtrar logins fallidos</div><small class="text-muted">Acción LOGIN_FAIL</small></div>
            </a>
          </div>
          <div class="col-md-6">
            <a href="{{ route('bitacora.index') }}?accion=ACCESO_DENEGADO" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-reset rounded" style="background:#f8f9fb;border:1px solid var(--cup-border);">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(245,158,11,0.10);display:flex;align-items:center;justify-content:center;color:var(--cup-warning);"><i class="bi bi-x-octagon fs-4"></i></div>
              <div><div style="font-weight:600;">Filtrar accesos denegados</div><small class="text-muted">Acción ACCESO_DENEGADO</small></div>
            </a>
          </div>
          @if(Route::has('usuarios.index'))
          <div class="col-md-6">
            <a href="{{ route('usuarios.index') }}" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-reset rounded" style="background:#f8f9fb;border:1px solid var(--cup-border);">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(13,44,94,0.08);display:flex;align-items:center;justify-content:center;color:var(--cup-primary);"><i class="bi bi-people fs-4"></i></div>
              <div><div style="font-weight:600;">Ver usuarios</div><small class="text-muted">Listado (solo lectura)</small></div>
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
          Como <strong>Auditor</strong> tenés acceso de solo lectura a la bitácora del
          sistema y a los listados de seguridad. Tu rol es supervisar la actividad del sistema.
        </p>
      </div>
    </div>
  </div>
</div>

@endsection
