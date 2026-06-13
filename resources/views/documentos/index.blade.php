@extends('layouts.base')

@section('titulo', 'Documentación de Postulantes')

@section('contenido')

<div class="page-header mb-4">
  <h1 class="mb-1">
    <i class="bi bi-folder2-open me-2"></i>
    Documentación de Postulantes
  </h1>
  <p class="text-muted mb-0">
    Validar requisitos presentados por los postulantes inscritos
  </p>
</div>

<form method="GET" class="row g-2 align-items-center mb-3">
  <div class="col-md-6">
    <div class="input-group">
      <span class="input-group-text bg-white">
        <i class="bi bi-search text-muted"></i>
      </span>
      <input type="text" name="q" value="{{ $q ?? '' }}"
             class="form-control"
             placeholder="Buscar postulante por nombre o CI...">
    </div>
  </div>

  <div class="col-md-4">
    <select name="periodo_id" class="form-select">
      <option value="todos" {{ (string)($periodoId ?? '') === 'todos' ? 'selected' : '' }}>Todos los periodos</option>
      @foreach($periodos as $per)
        <option value="{{ $per->id }}" {{ (int)($periodoId ?? 0) === (int)$per->id ? 'selected' : '' }}>
          Periodo #{{ $per->id }} {{ $per->activo ? '(activo)' : '(cerrado)' }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-md-2">
    <button type="submit" class="btn btn-cup-primary w-100">
      <i class="bi bi-funnel me-1"></i> Filtrar
    </button>
  </div>
</form>

<div class="panel-cup">
  <div class="panel-cup-body p-0">
    <div class="table-responsive">
      <table class="table table-cup mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>CI</th>
            <th>POSTULANTE</th>
            <th>1RA CARRERA</th>
            <th class="text-center">REQUISITOS</th>
            <th class="text-center">ESTADO</th>
            <th class="text-center">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          @forelse($inscripciones as $i => $insc)
            @php
              $totalReq = \App\Models\Requisito::where('periodo_id', $insc->periodo_id)->count();
              $cumplidos = $insc->documentos->where('cumplido', true)->count();
              $carrera1 = $insc->postulacionCarreras->where('prioridad', 1)->first();
              $habilitado = $totalReq > 0 && $cumplidos >= $totalReq;
            @endphp
            <tr>
              <td>{{ $inscripciones->firstItem() + $i }}</td>
              <td><small class="text-muted">{{ $insc->postulante->persona->ci }}</small></td>
              <td><strong>{{ $insc->postulante->persona->nombre }}</strong></td>
              <td><small>{{ optional(optional($carrera1)->carrera)->nombre ?? '—' }}</small></td>
              <td class="text-center">
                <span class="badge {{ $habilitado ? 'bg-success' : 'bg-secondary' }}">
                  {{ $cumplidos }} / {{ $totalReq }}
                </span>
              </td>
              <td class="text-center">
                @if($habilitado)
                  <span class="badge bg-success"><i class="bi bi-check-circle"></i> Habilitado</span>
                @elseif($cumplidos > 0)
                  <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> En proceso</span>
                @else
                  <span class="badge bg-secondary"><i class="bi bi-circle"></i> Pendiente</span>
                @endif
              </td>
              <td class="text-center">
                <a href="{{ route('documentos.show', $insc) }}" class="btn btn-sm btn-cup-primary">
                  <i class="bi bi-check2-square me-1"></i> Validar
                </a>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted py-4">Sin inscripciones para validar.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@if($inscripciones->hasPages())
  <div class="mt-3 d-flex justify-content-center">
    {{ $inscripciones->links('pagination::bootstrap-5') }}
  </div>
@endif

@endsection

