@extends('layouts.base')

@section('titulo', 'Estadísticas por grupo')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-people me-2"></i>Estadísticas por Grupo</h1>
    <p class="page-subtitle">CU27 — Ocupación, rendimiento y composición
      @if($periodo) · Periodo #{{ $periodo->id }}@endif
    </p>
  </div>
  <a href="{{ route('estadisticas.dashboard') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Dashboard
  </a>
</div>

{{-- Selector de periodo --}}
@if($periodos->count() > 1)
<form method="GET" class="mb-3 d-flex align-items-center gap-2">
  <label class="form-label mb-0 fw-semibold">Periodo:</label>
  <select name="periodo_id" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
    @foreach($periodos as $p)
      <option value="{{ $p->id }}" {{ $periodo?->id == $p->id ? 'selected' : '' }}>
        Periodo #{{ $p->id }}
        ({{ $p->fecha_ini_curso }} — {{ $p->fecha_fin_curso }})
        {{ $p->activo ? '★ Activo' : '(cerrado)' }}
      </option>
    @endforeach
  </select>
</form>
@endif

<div class="panel-cup">
  <div class="panel-cup-body p-0">
    <div class="table-responsive">
      <table class="table-cup table mb-0">
        <thead>
          <tr>
            <th>Código</th>
            <th>Turno</th>
            <th>Materias y docentes</th>
            <th>Aula</th>
            <th class="text-center">Ocupación</th>
            <th class="text-center">Promedio<br>grupo</th>
            <th class="text-center">% Aprobados</th>
          </tr>
        </thead>
        <tbody>
          @forelse($grupos as $g)
            @php
              $pct = $g->cupo_max > 0 ? round($g->inscritos_actuales * 100 / $g->cupo_max) : 0;
              $ocColor = $pct >= 90 ? 'bg-danger' : ($pct >= 70 ? 'bg-warning' : 'bg-success');
            @endphp
            <tr>
              <td><span class="badge-cup badge-modulo">{{ $g->codigo }}</span></td>
              <td>{{ optional($g->horario)->turno ?? optional($g->horario)->codigo ?? '—' }}</td>
              <td>
                @forelse($g->grupoMaterias->sortBy('orden') as $bloque)
                  <div class="small mb-1">
                    <span class="badge bg-secondary me-1">{{ optional($bloque->materia)->sigla ?? '—' }}</span>
                    {{ optional(optional($bloque->docente)->persona)->nombre ?? '<span class="text-muted fst-italic">Sin docente</span>' }}
                  </div>
                @empty
                  <span class="text-muted">Sin materias configuradas</span>
                @endforelse
              </td>
              <td class="small">{{ optional($g->aula)->codigo ?? '—' }}</td>
              <td class="text-center" style="min-width:110px">
                <div class="d-flex align-items-center gap-1 justify-content-center">
                  <div style="flex:1;height:6px;background:#e9ecef;border-radius:3px;overflow:hidden">
                    <div class="{{ $ocColor }}" style="width:{{ $pct }}%;height:100%;border-radius:3px"></div>
                  </div>
                  <small class="fw-semibold">{{ $g->inscritos_actuales }}/{{ $g->cupo_max }}</small>
                </div>
              </td>
              <td class="text-center fw-semibold">
                @if($g->stats_promedio !== null)
                  <span class="{{ $g->stats_promedio >= 51 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($g->stats_promedio, 2) }}
                  </span>
                @else
                  <span class="text-muted small">—</span>
                @endif
              </td>
              <td class="text-center">
                @if($g->stats_pct_aprobados !== null)
                  <span class="{{ $g->stats_pct_aprobados >= 60 ? 'text-success' : ($g->stats_pct_aprobados >= 45 ? 'text-warning' : 'text-danger') }} fw-semibold">
                    {{ $g->stats_pct_aprobados }}%
                  </span>
                @else
                  <span class="text-muted small">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-4 text-muted">
                No hay grupos en este periodo.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
