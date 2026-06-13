@extends('layouts.base')

@section('titulo', 'Pre-asignación')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-list-ol me-2"></i>Pre-asignación — Ranking por mérito</h1>
    <p class="page-subtitle">CU24 — Ranking de aprobados (promedio DESC) antes de asignar carrera</p>
  </div>
  <a href="{{ route('admision.resultados') }}" class="btn btn-outline-secondary">
    <i class="bi bi-diagram-3 me-1"></i> Ver resultados
  </a>
</div>

@if($errors->any())
  <div class="alert alert-cup-danger"><i class="bi bi-exclamation-triangle me-2"></i>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
@endif

@if(!$periodo)
  <div class="alert alert-warning border-0" style="border-radius:8px"><i class="bi bi-exclamation-triangle me-2"></i>No hay periodo activo.</div>
@else
  <div class="row g-3 mb-4">
    <div class="col-md-3"><div class="kpi-card kpi-primary"><div class="kpi-value">{{ $resumen['total'] }}</div><div class="kpi-label">Con resultado</div></div></div>
    <div class="col-md-3"><div class="kpi-card kpi-success"><div class="kpi-value">{{ $resumen['aprobados'] }}</div><div class="kpi-label">Aprobados</div></div></div>
    <div class="col-md-3"><div class="kpi-card kpi-danger"><div class="kpi-value">{{ $resumen['reprobados'] }}</div><div class="kpi-label">Reprobados</div></div></div>
    <div class="col-md-3"><div class="kpi-card kpi-accent"><div class="kpi-value">{{ $resumen['asignados'] }}</div><div class="kpi-label">Ya asignados</div></div></div>
  </div>

  <div class="panel-cup mb-4">
    <div class="panel-cup-body d-flex justify-content-between align-items-center">
      <div>
        <strong>Ejecutar asignación de carreras</strong>
        <div class="text-muted small">Ordena por promedio y asigna 1ra preferencia → 2da → sin cupo, según cupos por carrera.</div>
      </div>
      <form id="form-ejecutar-asignacion" action="{{ route('admision.ejecutar') }}" method="POST">
        @csrf
        <button type="button" class="btn btn-cup-primary btn-lg"
                onclick="cupConfirmar({
                  titulo: 'Ejecutar asignación',
                  mensaje: '¿Ejecutar la asignación de carreras para el periodo #{{ $periodo->id }}?',
                  subtexto: 'Se asignará carrera definitiva por mérito y cupo a los {{ $resumen['aprobados'] }} aprobados.',
                  textoBoton: 'Sí, ejecutar',
                  tipo: 'warning',
                  formSelector: '#form-ejecutar-asignacion'
                })">
          <i class="bi bi-play-circle me-1"></i> Ejecutar asignación
        </button>
      </form>
    </div>
  </div>

  <div class="panel-cup">
    <div class="panel-cup-body p-0">
      <div class="table-responsive">
      <table class="table-cup table mb-0">
        <thead><tr><th>#</th><th>CI</th><th>Nombre</th><th class="text-center">Promedio</th></tr></thead>
        <tbody>
          @forelse($ranking as $r)
            <tr>
              <td><span class="badge-cup badge-modulo">{{ $ranking->firstItem() + $loop->index }}</span></td>
              <td>{{ $r->postulante->persona->ci ?? '—' }}</td>
              <td><strong>{{ $r->postulante->persona->nombre ?? '—' }}</strong></td>
              <td class="text-center"><span class="badge bg-success">{{ number_format($r->promedio_final, 2) }}</span></td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center py-4 text-muted">No hay aprobados. Cargá notas o corré PromediosDemoSeeder.</td></tr>
          @endforelse
        </tbody>
      </table>
      </div>
    </div>
  </div>

  @if($ranking->hasPages())
    <div class="mt-3 d-flex justify-content-center">{{ $ranking->links('pagination::bootstrap-5') }}</div>
  @endif
@endif

@endsection

