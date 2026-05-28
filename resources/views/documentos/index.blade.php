@extends('layouts.base')

@section('titulo', 'Documentación')

@section('contenido')

<div class="page-header mb-4">
  <h1><i class="bi bi-folder me-2"></i>Documentación de Postulantes</h1>
  <p class="page-subtitle">Revisión de documentos cargados por los postulantes inscritos</p>
</div>

@if(!$periodoActivo)
  <div class="alert alert-warning border-0" style="border-radius:8px">
    <i class="bi bi-exclamation-triangle me-2"></i>No hay periodo activo.
  </div>
@endif

<div class="panel-cup">
  <div class="panel-cup-body p-0">
    <div class="table-responsive">
    <table class="table-cup table mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>CI</th>
          <th>Postulante</th>
          <th>1ra Carrera</th>
          <th class="text-center">Docs Subidos</th>
          <th class="text-center">Aprobados</th>
          <th class="text-center">Rechazados</th>
          <th>Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($inscripciones as $inscripcion)
          @php
            $c1 = $inscripcion->postulacionCarreras->where('prioridad',1)->first();
          @endphp
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $inscripcion->postulante->persona->ci }}</td>
            <td><strong>{{ $inscripcion->postulante->persona->nombre }}</strong></td>
            <td>{{ $c1?->carrera->nombre ?? '—' }}</td>
            <td class="text-center">{{ $inscripcion->total_subidos }} / {{ $inscripcion->total_requisitos }}</td>
            <td class="text-center"><span class="badge-cup badge-activo">{{ $inscripcion->aprobados }}</span></td>
            <td class="text-center"><span class="badge-cup badge-inactivo">{{ $inscripcion->rechazados }}</span></td>
            <td>
              @if($inscripcion->completo)
                <span class="badge-cup badge-activo"><i class="bi bi-check-circle me-1"></i>Completo</span>
              @elseif($inscripcion->total_subidos == 0)
                <span class="badge bg-secondary">Sin docs</span>
              @else
                <span class="badge bg-warning text-dark">En revisión</span>
              @endif
            </td>
            <td class="text-end">
              <a href="{{ route('documentos.show', $inscripcion) }}" class="btn btn-sm btn-cup-primary">
                <i class="bi bi-folder2-open me-1"></i> Gestionar
              </a>
            </td>
          </tr>
        @empty
          <tr><td colspan="9" class="text-center py-4 text-muted">No hay postulantes inscritos en el periodo activo.</td></tr>
        @endforelse
      </tbody>
    </table>
    </div>
  </div>
</div>

@endsection
