@extends('layouts.base')

@section('titulo', 'Reportes')

@section('contenido')

<div class="page-header mb-4">
  <h1><i class="bi bi-file-earmark-text me-2"></i>Reportes</h1>
  <p class="page-subtitle">CU26 — Generar reportes en PDF, Excel o HTML</p>
</div>

{{-- Selector de periodo --}}
<div class="panel-cup mb-4">
  <div class="panel-cup-header">
    <span class="fw-semibold"><i class="bi bi-calendar3 me-2 text-primary"></i>Seleccionar periodo del reporte</span>
  </div>
  <div class="panel-cup-body">
    <div class="row align-items-center g-3">
      <div class="col-md-6">
        <select id="selector-periodo" class="form-select">
          @foreach($periodos as $p)
            <option value="{{ $p->id }}" {{ $p->id == $periodoActivo ? 'selected' : '' }}>
              Periodo #{{ $p->id }}
              ({{ \Carbon\Carbon::parse($p->fecha_ini_curso)->format('d/m/Y') }}
              — {{ \Carbon\Carbon::parse($p->fecha_fin_curso)->format('d/m/Y') }})
              {{ $p->activo ? '✓ ACTIVO' : '' }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <span id="periodo-label" class="text-muted small">
          Los botones de abajo generarán el reporte del periodo seleccionado.
        </span>
      </div>
    </div>
  </div>
</div>

{{-- Tabla de reportes --}}
<div class="panel-cup">
  <div class="panel-cup-body p-0">
    <div class="table-responsive">
      <table class="table-cup table mb-0 align-middle">
        <thead>
          <tr>
            <th>Reporte</th>
            <th class="text-end">Formatos</th>
          </tr>
        </thead>
        <tbody>
          @foreach($reportes as $tipo => $nombre)
            <tr>
              <td>
                <strong>{{ $nombre }}</strong><br>
                <small class="text-muted">{{ $tipo }}</small>
              </td>
              <td class="text-end">
                <a href="{{ route('reportes.pdf',   $tipo) }}"
                   class="btn btn-sm btn-outline-danger reporte-link"
                   data-tipo="{{ $tipo }}" data-formato="pdf">
                  <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                </a>
                <a href="{{ route('reportes.excel', $tipo) }}"
                   class="btn btn-sm btn-outline-success reporte-link"
                   data-tipo="{{ $tipo }}" data-formato="excel">
                  <i class="bi bi-file-earmark-excel me-1"></i>Excel
                </a>
                <a href="{{ route('reportes.html',  $tipo) }}"
                   class="btn btn-sm btn-outline-primary reporte-link"
                   data-tipo="{{ $tipo }}" data-formato="html"
                   target="_blank">
                  <i class="bi bi-filetype-html me-1"></i>HTML
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
  const rutas = {
    pdf:   @json(url('reportes')),
    excel: @json(url('reportes')),
    html:  @json(url('reportes')),
  };

  function actualizarLinks() {
    const periodoId = document.getElementById('selector-periodo').value;
    document.querySelectorAll('.reporte-link').forEach(function (a) {
      const tipo    = a.dataset.tipo;
      const formato = a.dataset.formato;
      a.href = '/reportes/' + tipo + '/' + formato + '?periodo_id=' + periodoId;
    });
  }

  document.getElementById('selector-periodo').addEventListener('change', actualizarLinks);

  // Inicializar con el periodo seleccionado al cargar
  actualizarLinks();
})();
</script>
@endpush
