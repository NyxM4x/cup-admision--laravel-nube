@extends('layouts.base')

@section('titulo', 'Dashboard Administrador')

@section('contenido')

@php
  try { $totalUsuarios = \App\Models\User::count(); } catch (\Exception $e) { $totalUsuarios = 0; }
  try { $totalRoles = \App\Models\Rol::where('activo', true)->count(); } catch (\Exception $e) { $totalRoles = 0; }
  try { $totalAulas = \App\Models\Aula::where('activo', true)->count(); } catch (\Exception $e) { $totalAulas = 0; }
  try { $totalBitacora = \App\Models\Bitacora::count(); } catch (\Exception $e) { $totalBitacora = 0; }
@endphp

<div class="page-header mb-4">
  <h1><i class="bi bi-shield-fill-check me-2"></i>Panel de Administración</h1>
  <p class="page-subtitle">
    Bienvenido/a, <strong>{{ Auth::user()->name }}</strong>
    — Rol: <strong>{{ Auth::user()->rol->nombre ?? 'Sin rol' }}</strong>
  </p>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-6 col-lg-3">
    <a href="{{ route('usuarios.index') }}" class="text-decoration-none text-reset">
      <div class="kpi-card kpi-primary h-100">
        <div class="kpi-icon"><i class="bi bi-people-fill"></i></div>
        <div class="kpi-value">{{ $totalUsuarios }}</div>
        <div class="kpi-label">Usuarios totales</div>
      </div>
    </a>
  </div>
  <div class="col-md-6 col-lg-3">
    <a href="{{ route('roles.index') }}" class="text-decoration-none text-reset">
      <div class="kpi-card kpi-success h-100">
        <div class="kpi-icon"><i class="bi bi-shield-lock"></i></div>
        <div class="kpi-value">{{ $totalRoles }}</div>
        <div class="kpi-label">Roles configurados</div>
      </div>
    </a>
  </div>
  <div class="col-md-6 col-lg-3">
    <a href="{{ route('aulas.index') }}" class="text-decoration-none text-reset">
      <div class="kpi-card kpi-accent h-100">
        <div class="kpi-icon"><i class="bi bi-door-open-fill"></i></div>
        <div class="kpi-value">{{ $totalAulas }}</div>
        <div class="kpi-label">Aulas activas</div>
      </div>
    </a>
  </div>
  <div class="col-md-6 col-lg-3">
    <a href="{{ route('bitacora.index') }}" class="text-decoration-none text-reset">
      <div class="kpi-card kpi-warning h-100">
        <div class="kpi-icon"><i class="bi bi-journal-text"></i></div>
        <div class="kpi-value">{{ $totalBitacora }}</div>
        <div class="kpi-label">Eventos en bitácora</div>
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
            <a href="{{ route('usuarios.index') }}" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-reset rounded" style="background:#f8f9fb;border:1px solid var(--cup-border);">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(30,95,168,0.10);display:flex;align-items:center;justify-content:center;color:var(--cup-primary-light);"><i class="bi bi-people fs-4"></i></div>
              <div><div style="font-weight:600;">Gestionar usuarios</div><small class="text-muted">Crear, editar e inactivar usuarios</small></div>
            </a>
          </div>
          <div class="col-md-6">
            <a href="{{ route('roles.index') }}" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-reset rounded" style="background:#f8f9fb;border:1px solid var(--cup-border);">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(25,135,84,0.10);display:flex;align-items:center;justify-content:center;color:var(--cup-success);"><i class="bi bi-shield-lock fs-4"></i></div>
              <div><div style="font-weight:600;">Roles y permisos</div><small class="text-muted">Configurar accesos por rol</small></div>
            </a>
          </div>
          <div class="col-md-6">
            <a href="{{ route('aulas.index') }}" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-reset rounded" style="background:#f8f9fb;border:1px solid var(--cup-border);">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(44,123,229,0.10);display:flex;align-items:center;justify-content:center;color:var(--cup-accent);"><i class="bi bi-door-open fs-4"></i></div>
              <div><div style="font-weight:600;">Catálogo de aulas</div><small class="text-muted">Gestionar aulas del CUP</small></div>
            </a>
          </div>
          <div class="col-md-6">
            <a href="{{ route('bitacora.index') }}" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-reset rounded" style="background:#f8f9fb;border:1px solid var(--cup-border);">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(245,158,11,0.10);display:flex;align-items:center;justify-content:center;color:var(--cup-warning);"><i class="bi bi-journal-text fs-4"></i></div>
              <div><div style="font-weight:600;">Ver bitácora</div><small class="text-muted">Auditoría de acciones</small></div>
            </a>
          </div>
          @if(Route::has('postulantes.index'))
          <div class="col-md-6">
            <a href="{{ route('postulantes.index') }}" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-reset rounded" style="background:#f8f9fb;border:1px solid var(--cup-border);">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(30,95,168,0.10);display:flex;align-items:center;justify-content:center;color:var(--cup-primary-light);"><i class="bi bi-person-vcard fs-4"></i></div>
              <div><div style="font-weight:600;">Postulantes</div><small class="text-muted">Registro de postulantes</small></div>
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
          Como <strong>Administrador</strong> tenés control total del sistema: gestión de
          usuarios, roles y permisos, configuración de aulas y acceso completo a la
          bitácora de auditoría. Usá los accesos rápidos para las tareas más frecuentes.
        </p>
      </div>
    </div>
  </div>
</div>

@endsection
