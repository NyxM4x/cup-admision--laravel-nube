@extends('layouts.base')

@section('titulo', 'Estadísticas')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-bar-chart me-2"></i>Estadísticas de Admisión</h1>
    <p class="page-subtitle">CU27 — Indicadores del periodo @if($periodo) #{{ $periodo->id }} @endif</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('estadisticas.docentes') }}" class="btn btn-outline-secondary"><i class="bi bi-person-workspace me-1"></i> Por docente</a>
    <a href="{{ route('estadisticas.grupos') }}" class="btn btn-outline-secondary"><i class="bi bi-people me-1"></i> Por grupo</a>
  </div>
</div>

<form method="GET" class="row g-2 mb-4" style="max-width:360px">
  <div class="col-9">
    <select name="periodo_id" class="form-select" onchange="this.form.submit()">
      @foreach($periodos as $per)
        <option value="{{ $per->id }}" {{ $periodo && $periodo->id===$per->id?'selected':'' }}>Periodo #{{ $per->id }} {{ $per->activo?'(activo)':'' }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-3"><button class="btn btn-cup-primary w-100"><i class="bi bi-funnel"></i></button></div>
</form>

<div class="row g-4">
  <div class="col-md-5">
    <div class="panel-cup"><div class="panel-cup-body">
      <strong><i class="bi bi-pie-chart me-1"></i> Distribución por carrera (1ra preferencia)</strong>
      <canvas id="chartCarrera" height="220"></canvas>
    </div></div>
  </div>
  <div class="col-md-7">
    <div class="panel-cup"><div class="panel-cup-body">
      <strong><i class="bi bi-bar-chart me-1"></i> Aprobados vs Reprobados</strong>
      <canvas id="chartAprob" height="220"></canvas>
    </div></div>
  </div>
  <div class="col-12">
    <div class="panel-cup"><div class="panel-cup-body">
      <strong><i class="bi bi-bar-chart-line me-1"></i> Promedio por materia <span class="text-muted small">(demo — depende de notas CU21-23)</span></strong>
      <canvas id="chartMateria" height="90"></canvas>
    </div></div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  const cupColors = ['#1e5fa8','#198754','#f59e0b','#dc2626','#0dcaf0','#6f42c1','#20c997','#fd7e14'];

  const carreraData = @json($porCarrera);
  new Chart(document.getElementById('chartCarrera'), {
    type: 'pie',
    data: { labels: Object.keys(carreraData), datasets: [{ data: Object.values(carreraData), backgroundColor: cupColors }] },
    options: { plugins: { legend: { position: 'bottom' } } }
  });

  const ap = @json($aprobReprob);
  new Chart(document.getElementById('chartAprob'), {
    type: 'bar',
    data: { labels: ['Aprobados','Reprobados'], datasets: [{ label: 'Postulantes', data: [ap.aprobados, ap.reprobados], backgroundColor: ['#198754','#dc2626'] }] },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
  });

  const mat = @json($promedioMateria);
  new Chart(document.getElementById('chartMateria'), {
    type: 'bar',
    data: { labels: Object.keys(mat), datasets: [{ label: 'Promedio', data: Object.values(mat), backgroundColor: '#1e5fa8' }] },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 100 } } }
  });
</script>
@endpush

@endsection
