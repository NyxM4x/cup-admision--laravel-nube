@extends('layouts.base')

@section('titulo', 'Estadísticas por docente')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-person-workspace me-2"></i>Estadísticas por Docente</h1>
    <p class="page-subtitle">CU27 — Grupos, rendimiento y satisfacción
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
            <th>Docente</th>
            <th>Materia</th>
            <th>Profesión</th>
            <th class="text-center">Grupos<br>asignados</th>
            <th class="text-center">Promedio<br>estudiantes</th>
            <th class="text-center">% Aprobados</th>
            <th class="text-center">Satisfacción</th>
          </tr>
        </thead>
        <tbody>
          @forelse($docentes as $d)
            <tr>
              <td><strong>{{ $d->nombre }}</strong></td>
              <td>
                @if($d->materia && $d->materia !== 'Sin asignar')
                  <span class="badge bg-primary">{{ $d->materia }}</span>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
              <td class="small text-muted">{{ $d->profesion ?? '—' }}</td>
              <td class="text-center">
                @if($d->grupos > 0)
                  <span class="badge-cup badge-modulo">{{ $d->grupos }}</span>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
              <td class="text-center">
                @if($d->promedio_ref !== null)
                  <span class="{{ $d->promedio_ref >= 51 ? 'text-success' : 'text-danger' }} fw-semibold">
                    {{ number_format($d->promedio_ref, 2) }}
                  </span>
                @else
                  <span class="text-muted small">Sin notas</span>
                @endif
              </td>
              <td class="text-center">
                @if($d->pct_aprobados !== null)
                  <span class="{{ $d->pct_aprobados >= 60 ? 'text-success' : ($d->pct_aprobados >= 45 ? 'text-warning' : 'text-danger') }} fw-semibold">
                    {{ $d->pct_aprobados }}%
                  </span>
                @else
                  <span class="text-muted small">—</span>
                @endif
              </td>
              <td class="text-center" style="min-width:120px">
                @if($d->satisfaccion !== null)
                  @php
                    $sat = $d->satisfaccion;
                    $color = $sat >= 70 ? '#28a745' : ($sat >= 50 ? '#ffc107' : '#dc3545');
                    $bgColor = $sat >= 70 ? '#d4edda' : ($sat >= 50 ? '#fff3cd' : '#f8d7da');
                  @endphp
                  <div class="d-flex align-items-center gap-1 justify-content-center">
                    <div style="flex:1;height:8px;background:#e9ecef;border-radius:4px;overflow:hidden">
                      <div style="width:{{ min(100, $sat) }}%;height:100%;background:{{ $color }};border-radius:4px"></div>
                    </div>
                    <small style="color:{{ $color }};font-weight:600;white-space:nowrap">{{ $sat }}%</small>
                  </div>
                @else
                  <span class="text-muted small">Sin datos</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-4 text-muted">No hay docentes activos.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection
