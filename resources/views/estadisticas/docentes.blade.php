@extends('layouts.base')

@section('titulo', 'Estadísticas por docente')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-person-workspace me-2"></i>Estadísticas por Docente</h1>
    <p class="page-subtitle">CU27 — Grupos asignados y rendimiento @if($periodo) (periodo #{{ $periodo->id }}) @endif</p>
  </div>
  <a href="{{ route('estadisticas.dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Dashboard</a>
</div>

<div class="alert alert-cup-warning">
  <i class="bi bi-info-circle me-2"></i>
  El <strong>% aprobados</strong> y el <strong>promedio</strong> son referenciales (globales del periodo): las notas por grupo dependen de CU21-23 (compañero).
</div>

<div class="panel-cup">
  <div class="panel-cup-body p-0">
    <div class="table-responsive">
    <table class="table-cup table mb-0">
      <thead><tr><th>Docente</th><th>Materia</th><th>Profesión</th><th class="text-center">Grupos asignados</th><th class="text-center">Promedio ref.</th><th class="text-center">% aprobados ref.</th></tr></thead>
      <tbody>
        @forelse($docentes as $d)
          <tr>
            <td><strong>{{ $d->nombre }}</strong></td>
            <td>{{ $d->materia ?? '—' }}</td>
            <td>{{ $d->profesion ?? '—' }}</td>
            <td class="text-center"><span class="badge-cup badge-modulo">{{ $d->grupos }}</span></td>
            <td class="text-center">{{ $d->promedio_ref !== null ? number_format($d->promedio_ref, 2) : '—' }}</td>
            <td class="text-center">{{ $d->pct_aprobados !== null ? $d->pct_aprobados.'%' : '—' }}</td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center py-4 text-muted">No hay docentes activos.</td></tr>
        @endforelse
      </tbody>
    </table>
    </div>
  </div>
</div>

@endsection
