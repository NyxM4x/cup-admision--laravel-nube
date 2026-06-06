@extends('layouts.base')

@section('titulo', 'Resultados de asignación')

@section('contenido')

@php
  $badges = [
    'aprobado' => 'secondary', 'reprobado' => 'danger',
    'admitido_primera' => 'success', 'admitido_segunda' => 'info',
    'no_admitido_sin_cupo' => 'warning', 'lista_espera' => 'secondary',
  ];
  $labels = [
    'aprobado' => 'Aprobado', 'reprobado' => 'Reprobado',
    'admitido_primera' => 'Admitido 1ra', 'admitido_segunda' => 'Admitido 2da',
    'no_admitido_sin_cupo' => 'Sin cupo', 'lista_espera' => 'Lista espera',
  ];
@endphp

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-diagram-3 me-2"></i>Resultados de asignación</h1>
    <p class="page-subtitle">CU24 — Asignaciones de carrera por mérito y cupo</p>
  </div>
  <a href="{{ route('admision.preasignacion') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Pre-asignación
  </a>
</div>

@if(session('stats_asignacion'))
  @php $s = session('stats_asignacion'); @endphp
  <div class="alert alert-cup-success">
    <i class="bi bi-check-circle me-2"></i>
    <strong>Asignación ejecutada.</strong>
    Aprobados: {{ $s['total_aprobados'] }} ·
    Admitidos 1ra: {{ $s['admitidos_primera'] }} ·
    Admitidos 2da: {{ $s['admitidos_segunda'] }} ·
    Sin cupo: {{ $s['sin_cupo'] }}
  </div>
@endif

<form method="GET" class="row g-2 align-items-center mb-3">
  <div class="col-md-4">
    <div class="input-group">
      <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
      <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="Buscar por nombre o CI...">
    </div>
  </div>
  <div class="col-md-3">
    <select name="estado" class="form-select">
      <option value="todos" {{ ($estado ?? '')==='todos'?'selected':'' }}>Todos los estados</option>
      @foreach($labels as $k => $v)
        <option value="{{ $k }}" {{ ($estado ?? '')===$k?'selected':'' }}>{{ $v }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3">
    <select name="carrera_id" class="form-select">
      <option value="todos" {{ (string)($carreraId ?? '')==='todos'?'selected':'' }}>Todas las carreras</option>
      @foreach($carreras as $c)
        <option value="{{ $c->id }}" {{ (int)($carreraId ?? 0)===(int)$c->id?'selected':'' }}>{{ $c->nombre }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-2">
    <button type="submit" class="btn btn-cup-primary w-100"><i class="bi bi-funnel me-1"></i> Filtrar</button>
  </div>
</form>

<div class="panel-cup">
  <div class="panel-cup-body p-0">
    <div class="table-responsive">
    <table class="table-cup table mb-0">
      <thead><tr><th>Ranking</th><th>CI</th><th>Nombre</th><th class="text-center">Promedio</th><th>Carrera asignada</th><th>Estado</th></tr></thead>
      <tbody>
        @forelse($resultados as $r)
          <tr>
            <td>{{ $r->posicion_ranking_general ?? '—' }}</td>
            <td>{{ $r->postulante->persona->ci ?? '—' }}</td>
            <td><strong>{{ $r->postulante->persona->nombre ?? '—' }}</strong></td>
            <td class="text-center">{{ number_format($r->promedio_final, 2) }}</td>
            <td>{{ optional($r->carreraAsignada)->nombre ?? '—' }}</td>
            <td><span class="badge bg-{{ $badges[$r->estado_admision] ?? 'secondary' }}">{{ $labels[$r->estado_admision] ?? $r->estado_admision }}</span></td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center py-4 text-muted">Sin resultados. Ejecutá la asignación desde Pre-asignación.</td></tr>
        @endforelse
      </tbody>
    </table>
    </div>
  </div>
</div>

@if($resultados instanceof \Illuminate\Pagination\LengthAwarePaginator && $resultados->hasPages())
  <div class="mt-3 d-flex justify-content-center">{{ $resultados->links() }}</div>
@endif

@endsection
