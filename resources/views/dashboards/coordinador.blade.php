@extends('layouts.base')

@section('titulo', 'Dashboard Coordinador')

@section('contenido')

@php
  try { $totalPostulantes = \App\Models\Postulante::count(); } catch (\Exception $e) { $totalPostulantes = 0; }
  try { $totalInscripciones = \App\Models\Inscripcion::count(); } catch (\Exception $e) { $totalInscripciones = 0; }
  try { $docsPendientes = \App\Models\DocumentoPostulante::where('estado', 'pendiente')->count(); } catch (\Exception $e) { $docsPendientes = 0; }
  try { $totalAulas = \App\Models\Aula::where('activo', true)->count(); } catch (\Exception $e) { $totalAulas = 0; }
@endphp

<div class="page-header mb-4">
  <h1><i class="bi bi-clipboard-data-fill me-2"></i>Panel del Coordinador CUP</h1>
  <p class="page-subtitle">
    Bienvenido/a, <strong>{{ Auth::user()->name }}</strong>
    — Rol: <strong>{{ Auth::user()->rol->nombre ?? 'Sin rol' }}</strong>
  </p>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-6 col-lg-3">
    @if(Route::has('postulantes.index'))<a href="{{ route('postulantes.index') }}" class="text-decoration-none text-reset">@endif
      <div class="kpi-card kpi-primary h-100">
        <div class="kpi-icon"><i class="bi bi-person-vcard"></i></div>
        <div class="kpi-value">{{ $totalPostulantes }}</div>
        <div class="kpi-label">Postulantes registrados</div>
      </div>
    @if(Route::has('postulantes.index'))</a>@endif
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="kpi-card kpi-success h-100">
      <div class="kpi-icon"><i class="bi bi-check2-circle"></i></div>
      <div class="kpi-value">{{ $totalInscripciones }}</div>
      <div class="kpi-label">Inscripciones confirmadas</div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    @if(Route::has('documentos.index'))<a href="{{ route('documentos.index') }}" class="text-decoration-none text-reset">@endif
      <div class="kpi-card kpi-warning h-100">
        <div class="kpi-icon"><i class="bi bi-folder"></i></div>
        <div class="kpi-value">{{ $docsPendientes }}</div>
        <div class="kpi-label">Documentos pendientes</div>
      </div>
    @if(Route::has('documentos.index'))</a>@endif
  </div>
  <div class="col-md-6 col-lg-3">
    <a href="{{ route('aulas.index') }}" class="text-decoration-none text-reset">
      <div class="kpi-card kpi-accent h-100">
        <div class="kpi-icon"><i class="bi bi-door-open"></i></div>
        <div class="kpi-value">{{ $totalAulas }}</div>
        <div class="kpi-label">Aulas disponibles</div>
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
          @if(Route::has('postulantes.index'))
          <div class="col-md-6">
            <a href="{{ route('postulantes.index') }}" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-reset rounded" style="background:#f8f9fb;border:1px solid var(--cup-border);">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(30,95,168,0.10);display:flex;align-items:center;justify-content:center;color:var(--cup-primary-light);"><i class="bi bi-person-vcard fs-4"></i></div>
              <div><div style="font-weight:600;">Postulantes</div><small class="text-muted">Gestionar postulantes</small></div>
            </a>
          </div>
          @endif
          @if(Route::has('inscripciones.index'))
          <div class="col-md-6">
            <a href="{{ route('inscripciones.index') }}" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-reset rounded" style="background:#f8f9fb;border:1px solid var(--cup-border);">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(25,135,84,0.10);display:flex;align-items:center;justify-content:center;color:var(--cup-success);"><i class="bi bi-check2-circle fs-4"></i></div>
              <div><div style="font-weight:600;">Inscripciones</div><small class="text-muted">Confirmar inscripciones</small></div>
            </a>
          </div>
          @endif
          @if(Route::has('documentos.index'))
          <div class="col-md-6">
            <a href="{{ route('documentos.index') }}" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-reset rounded" style="background:#f8f9fb;border:1px solid var(--cup-border);">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(245,158,11,0.10);display:flex;align-items:center;justify-content:center;color:var(--cup-warning);"><i class="bi bi-folder2-open fs-4"></i></div>
              <div><div style="font-weight:600;">Documentación</div><small class="text-muted">Revisar documentos</small></div>
            </a>
          </div>
          @endif
          <div class="col-md-6">
            <a href="{{ route('aulas.index') }}" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-reset rounded" style="background:#f8f9fb;border:1px solid var(--cup-border);">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(44,123,229,0.10);display:flex;align-items:center;justify-content:center;color:var(--cup-accent);"><i class="bi bi-door-open fs-4"></i></div>
              <div><div style="font-weight:600;">Aulas</div><small class="text-muted">Catálogo de aulas</small></div>
            </a>
          </div>
          @if(Route::has('bitacora.index'))
          <div class="col-md-6">
            <a href="{{ route('bitacora.index') }}" class="d-flex align-items-center gap-3 p-3 text-decoration-none text-reset rounded" style="background:#f8f9fb;border:1px solid var(--cup-border);">
              <div style="width:42px;height:42px;border-radius:10px;background:rgba(13,44,94,0.08);display:flex;align-items:center;justify-content:center;color:var(--cup-primary);"><i class="bi bi-journal-text fs-4"></i></div>
              <div><div style="font-weight:600;">Bitácora</div><small class="text-muted">Auditar procesos críticos</small></div>
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
          Como <strong>Coordinador CUP</strong> gestionás el proceso de admisión:
          postulantes, inscripciones y documentación. Tenés acceso a la bitácora
          para auditar los procesos críticos.
        </p>
      </div>
    </div>
  </div>
</div>

@endsection
