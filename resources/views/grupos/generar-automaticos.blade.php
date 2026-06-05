@extends('layouts.base')

@section('titulo', 'Generar Grupos')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-magic me-2"></i>Generar Grupos Automáticamente</h1>
    <p class="page-subtitle">CU17 — Crea grupos por materia según los postulantes habilitados</p>
  </div>
  <a href="{{ route('grupos.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

@if($errors->any())
  <div class="alert alert-cup-danger">
    <i class="bi bi-exclamation-triangle me-2"></i>
    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
  </div>
@endif

@if(!$periodo)
  <div class="alert alert-warning border-0" style="border-radius:8px">
    <i class="bi bi-exclamation-triangle me-2"></i>No hay un periodo activo. Activá o creá un periodo antes de generar grupos.
  </div>
@else
  <div class="panel-cup" style="max-width:720px">
    <div class="panel-cup-body">
      <div class="row g-3 mb-4">
        <div class="col-md-3">
          <div class="kpi-card kpi-primary">
            <div class="kpi-value">#{{ $periodo->id }}</div>
            <div class="kpi-label">Periodo activo</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="kpi-card kpi-success">
            <div class="kpi-value">{{ $habilitados }}</div>
            <div class="kpi-label">Habilitados</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="kpi-card kpi-accent">
            <div class="kpi-value">{{ $gruposPorMateria }}</div>
            <div class="kpi-label">Grupos / materia</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="kpi-card kpi-info">
            <div class="kpi-value">{{ $gruposPorMateria * $materiasCount }}</div>
            <div class="kpi-label">Total a generar</div>
          </div>
        </div>
      </div>

      <p class="text-muted">
        Se crearán <strong>CEIL({{ $habilitados }} / {{ \App\Http\Controllers\GrupoController::CUPO_DEFAULT }}) = {{ $gruposPorMateria }}</strong>
        grupos por cada una de las <strong>{{ $materiasCount }}</strong> materias activas, con cupo máximo de
        {{ \App\Http\Controllers\GrupoController::CUPO_DEFAULT }} alumnos.
        @if($existentes > 0)
          <br><i class="bi bi-info-circle me-1"></i>Ya existen <strong>{{ $existentes }}</strong> grupos en este periodo; la generación solo completa los faltantes (no duplica).
        @endif
      </p>

      <form id="form-generar-grupos" action="{{ route('grupos.generar-automaticos') }}" method="POST">
        @csrf
        <button type="button" class="btn btn-cup-primary btn-lg w-100"
                onclick="cupConfirmar({
                  titulo: 'Generar grupos automáticamente',
                  mensaje: '¿Generar los grupos para el periodo activo #{{ $periodo->id }}?',
                  subtexto: 'Se crearán {{ $gruposPorMateria }} grupos por materia ({{ $materiasCount }} materias). No se duplican los existentes.',
                  textoBoton: 'Sí, generar',
                  tipo: 'warning',
                  formSelector: '#form-generar-grupos'
                })">
          <i class="bi bi-magic me-1"></i> Generar grupos para el periodo activo
        </button>
      </form>
    </div>
  </div>
@endif

@endsection
