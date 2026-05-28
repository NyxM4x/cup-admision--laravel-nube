@extends('layouts.base')

@section('titulo', 'Dashboard Postulante')

@section('contenido')

<div class="page-header mb-4">
  <h1><i class="bi bi-person-circle me-2"></i>Panel del Postulante</h1>
  <p class="page-subtitle">
    Bienvenido/a, <strong>{{ Auth::user()->name }}</strong>
    — Rol: <strong>{{ Auth::user()->rol->nombre ?? 'Sin rol' }}</strong>
  </p>
</div>

<div class="alert alert-warning border-0 d-flex align-items-start gap-2" style="border-radius:8px">
  <i class="bi bi-info-circle mt-1"></i>
  <div>
    <strong>Funcionalidad de postulante en desarrollo.</strong> Próximamente podrás
    completar tu inscripción, subir documentación y consultar resultados desde este panel.
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="kpi-card kpi-primary h-100">
      <div class="kpi-icon"><i class="bi bi-clipboard-check"></i></div>
      <div class="kpi-value" style="font-size:1.25rem;">Pendiente</div>
      <div class="kpi-label">Mi Inscripción</div>
      <a href="#" class="btn btn-sm btn-cup-primary mt-3 disabled">Ver detalle</a>
    </div>
  </div>
  <div class="col-md-4">
    <div class="kpi-card kpi-warning h-100">
      <div class="kpi-icon"><i class="bi bi-folder"></i></div>
      <div class="kpi-value" style="font-size:1.25rem;">0 de 5</div>
      <div class="kpi-label">Mis Documentos cargados</div>
      <a href="#" class="btn btn-sm btn-cup-primary mt-3 disabled">Cargar documentos</a>
    </div>
  </div>
  <div class="col-md-4">
    <div class="kpi-card kpi-accent h-100">
      <div class="kpi-icon"><i class="bi bi-graph-up"></i></div>
      <div class="kpi-value" style="font-size:1.25rem;">No disponibles</div>
      <div class="kpi-label">Resultados</div>
      <button class="btn btn-sm btn-outline-secondary mt-3" disabled>Ver resultados</button>
    </div>
  </div>
</div>

<div class="panel-cup">
  <div class="panel-cup-header">
    <strong><i class="bi bi-info-circle me-1"></i> Bienvenida al proceso de admisión</strong>
  </div>
  <div class="panel-cup-body">
    <p class="text-muted mb-0" style="font-size:0.92rem;line-height:1.6;">
      Bienvenido/a al <strong>Curso Preuniversitario de la FICCT (UAGRM)</strong>. Desde este panel
      vas a poder gestionar todo tu proceso de admisión: completar tu inscripción a las carreras,
      subir la documentación requerida y consultar tus resultados. Estas funciones se irán
      habilitando en las próximas iteraciones del sistema.
    </p>
  </div>
</div>

@endsection
