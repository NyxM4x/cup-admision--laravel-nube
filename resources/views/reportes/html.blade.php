@extends('layouts.base')

@section('titulo', $titulo)

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-filetype-html me-2"></i>{{ $titulo }}</h1>
    <p class="page-subtitle">Reporte HTML @if($periodo) · Periodo #{{ $periodo->id }} @endif · {{ count($rows) }} filas</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('reportes.pdf', $tipo) }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-pdf me-1"></i> PDF</a>
    <a href="{{ route('reportes.excel', $tipo) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-excel me-1"></i> Excel</a>
    <a href="{{ route('reportes.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Volver</a>
  </div>
</div>

<div class="panel-cup">
  <div class="panel-cup-body p-0">
    <div class="table-responsive">
    <table class="table-cup table mb-0">
      <thead><tr>@foreach($encabezados as $h)<th>{{ $h }}</th>@endforeach</tr></thead>
      <tbody>
        @forelse($rows as $row)
          <tr>@foreach($row as $cell)<td>{{ $cell }}</td>@endforeach</tr>
        @empty
          <tr><td colspan="{{ max(1, count($encabezados)) }}" class="text-center py-4 text-muted">Sin datos.</td></tr>
        @endforelse
      </tbody>
    </table>
    </div>
  </div>
</div>

@endsection
