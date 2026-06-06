@extends('layouts.base')

@section('titulo', 'Reportes')

@section('contenido')

<div class="page-header mb-4">
  <h1><i class="bi bi-file-earmark-text me-2"></i>Reportes</h1>
  <p class="page-subtitle">CU26 — Generar reportes en PDF, Excel o HTML</p>
</div>

<div class="panel-cup">
  <div class="panel-cup-body p-0">
    <div class="table-responsive">
    <table class="table-cup table mb-0 align-middle">
      <thead><tr><th>Reporte</th><th class="text-end">Formatos</th></tr></thead>
      <tbody>
        @foreach($reportes as $tipo => $nombre)
          <tr>
            <td><strong>{{ $nombre }}</strong><br><small class="text-muted">{{ $tipo }}</small></td>
            <td class="text-end">
              <a href="{{ route('reportes.pdf', $tipo) }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-earmark-pdf me-1"></i> PDF</a>
              <a href="{{ route('reportes.excel', $tipo) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-file-earmark-excel me-1"></i> Excel</a>
              <a href="{{ route('reportes.html', $tipo) }}" class="btn btn-sm btn-outline-primary" target="_blank"><i class="bi bi-filetype-html me-1"></i> HTML</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    </div>
  </div>
</div>

@endsection
