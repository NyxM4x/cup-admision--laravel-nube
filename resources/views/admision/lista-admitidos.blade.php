@extends('layouts.base')

@section('titulo', 'Lista de admitidos')

@section('contenido')

@php
  $badges = ['admitido_primera'=>'success','admitido_segunda'=>'info','reprobado'=>'danger','no_admitido_sin_cupo'=>'warning'];
  $labels = ['admitido_primera'=>'Admitido 1ra','admitido_segunda'=>'Admitido 2da','reprobado'=>'Reprobado','no_admitido_sin_cupo'=>'Sin cupo','aprobado'=>'Aprobado','lista_espera'=>'Lista espera'];
@endphp

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-award me-2"></i>Lista Final de Admitidos</h1>
    <p class="page-subtitle">CU25 — Lista oficial de admitidos y rechazados</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admision.exportar') }}" class="btn btn-outline-danger">
      <i class="bi bi-file-earmark-pdf me-1"></i> Exportar PDF
    </a>
    @if($periodo && !$periodo->lista_publicada)
      <form id="form-publicar-lista" action="{{ route('admision.publicar') }}" method="POST">
        @csrf
        <button type="button" class="btn btn-cup-primary"
                onclick="cupConfirmar({
                  titulo: 'Publicar lista oficial',
                  mensaje: '¿Publicar la lista de admitidos del periodo #{{ $periodo->id }}?',
                  subtexto: 'Quedará marcada como publicada oficialmente.',
                  textoBoton: 'Sí, publicar',
                  tipo: 'success',
                  formSelector: '#form-publicar-lista'
                })">
          <i class="bi bi-megaphone me-1"></i> Publicar lista
        </button>
      </form>
    @endif
  </div>
</div>

@if($errors->any())
  <div class="alert alert-cup-danger"><i class="bi bi-exclamation-triangle me-2"></i>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
@endif

@if($periodo && $periodo->lista_publicada)
  <div class="alert alert-cup-success">
    <i class="bi bi-patch-check-fill me-2"></i>
    <strong>Lista publicada oficialmente</strong>
    @if($periodo->fecha_publicacion) el {{ \Illuminate\Support\Carbon::parse($periodo->fecha_publicacion)->format('d/m/Y H:i') }} @endif.
  </div>
@endif

<form method="GET" class="row g-2 align-items-center mb-3">
  <div class="col-md-3">
    <div class="input-group">
      <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
      <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="Nombre o CI...">
    </div>
  </div>
  <div class="col-md-3">
    <select name="periodo_id" class="form-select">
      @foreach($periodos as $per)
        <option value="{{ $per->id }}" {{ (int)($periodoId ?? 0)===(int)$per->id?'selected':'' }}>Periodo #{{ $per->id }} {{ $per->activo?'(activo)':'' }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-2">
    <select name="estado" class="form-select">
      <option value="todos" {{ ($estado ?? '')==='todos'?'selected':'' }}>Todos</option>
      <option value="admitidos" {{ ($estado ?? '')==='admitidos'?'selected':'' }}>Admitidos</option>
      <option value="rechazados" {{ ($estado ?? '')==='rechazados'?'selected':'' }}>Rechazados</option>
    </select>
  </div>
  <div class="col-md-2">
    <select name="carrera_id" class="form-select">
      <option value="todos" {{ (string)($carreraId ?? '')==='todos'?'selected':'' }}>Carreras</option>
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
          <tr><td colspan="6" class="text-center py-4 text-muted">Sin resultados para este filtro.</td></tr>
        @endforelse
      </tbody>
    </table>
    </div>
  </div>
</div>

@if($resultados instanceof \Illuminate\Pagination\LengthAwarePaginator && $resultados->hasPages())
  <div class="mt-3 d-flex justify-content-center">
    {{ $resultados->links('pagination::bootstrap-5') }}
  </div>
@endif

@endsection
