@extends('layouts.base')

@section('titulo', 'Estadísticas por grupo')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-people me-2"></i>Estadísticas por Grupo</h1>
    <p class="page-subtitle">CU27 — Ocupación de grupos @if($periodo) (periodo #{{ $periodo->id }}) @endif</p>
  </div>
  <a href="{{ route('estadisticas.dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Dashboard</a>
</div>

<div class="panel-cup">
  <div class="panel-cup-body p-0">
    <div class="table-responsive">
    <table class="table-cup table mb-0">
      <thead><tr><th>Código</th><th>Materia</th><th>Docente</th><th>Aula</th><th>Horario</th><th class="text-center">Ocupación</th></tr></thead>
      <tbody>
        @forelse($grupos as $g)
          <tr>
            <td><span class="badge-cup badge-modulo">{{ $g->codigo }}</span></td>
            <td>{{ $g->materia->sigla ?? '—' }}</td>
            <td>{{ optional(optional($g->docente)->persona)->nombre ?? '—' }}</td>
            <td>{{ optional($g->aula)->codigo ?? '—' }}</td>
            <td>{{ optional($g->horario)->codigo ?? '—' }}</td>
            <td class="text-center">
              <span class="badge {{ $g->inscritos_actuales >= $g->cupo_max ? 'bg-danger' : 'bg-secondary' }}">{{ $g->inscritos_actuales }} / {{ $g->cupo_max }}</span>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center py-4 text-muted">No hay grupos en este periodo. Generalos en Gestión Académica → Grupos.</td></tr>
        @endforelse
      </tbody>
    </table>
    </div>
  </div>
</div>

@endsection
