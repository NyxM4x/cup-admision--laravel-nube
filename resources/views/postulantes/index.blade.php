@extends('layouts.base')

@section('titulo', 'Postulantes')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-person-vcard me-2"></i>Postulantes Inscritos</h1>
    <p class="page-subtitle">Postulantes registrados en el proceso de admisión</p>
  </div>
  <a href="{{ route('postulantes.create') }}" class="btn btn-cup-primary">
    <i class="bi bi-plus-circle me-1"></i> Registrar Postulante
  </a>
</div>

@if(!$periodoActivo)
  <div class="alert alert-warning border-0" style="border-radius:8px">
    <i class="bi bi-exclamation-triangle me-2"></i>No hay periodo activo. Los postulantes no pueden inscribirse.
  </div>
@endif

<x-buscador-cup
  :q="$q ?? ''"
  :estado="$estado ?? 'activos'"
  placeholder="Buscar postulante por nombre, CI o correo..."
/>

<div class="panel-cup">
  <div class="panel-cup-body p-0">
    <div class="table-responsive">
    <table class="table-cup table mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>CI</th>
          <th>Nombre</th>
          <th>Colegio</th>
          <th>1ra Carrera</th>
          <th>2da Carrera</th>
          <th>Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($postulantes as $postulante)
          @php
            $inscripcion = $postulante->inscripciones->first();
            $c1 = $inscripcion?->postulacionCarreras->where('prioridad',1)->first();
            $c2 = $inscripcion?->postulacionCarreras->where('prioridad',2)->first();
            $badges = [
              'pendiente' => 'warning',
              'inscrito'  => 'primary',
              'aprobado'  => 'success',
              'reprobado' => 'danger',
            ];
          @endphp
          <tr class="{{ ($postulante->activo ?? true) ? '' : 'table-secondary' }}">
            <td>{{ $postulantes->firstItem() + $loop->index }}</td>
            <td>{{ $postulante->persona->ci }}</td>
            <td><strong>{{ $postulante->persona->nombre }}</strong></td>
            <td>{{ $postulante->colegio }}</td>
            <td>{{ $c1?->carrera->nombre ?? '—' }}</td>
            <td>{{ $c2?->carrera->nombre ?? '—' }}</td>
            <td>
              <span class="badge bg-{{ $badges[$postulante->estado] ?? 'secondary' }}">
                {{ ucfirst($postulante->estado) }}
              </span>
              @if(!($postulante->activo ?? true))
                <span class="badge bg-dark ms-1" title="Inactivo">
                  <i class="bi bi-archive"></i> Inactivo
                </span>
              @endif
            </td>
            <td class="text-end">
              <a href="{{ route('postulantes.show', $postulante) }}" class="btn-action btn-action-view" title="Ver">
                <i class="bi bi-eye"></i>
              </a>
              <a href="{{ route('postulantes.edit', $postulante) }}" class="btn-action btn-action-edit" title="Editar">
                <i class="bi bi-pencil"></i>
              </a>
              @if($postulante->activo ?? true)
                <form method="POST" action="{{ route('postulantes.archivar', $postulante) }}"
                      class="d-inline" onsubmit="return confirm('¿Archivar este postulante?');">
                  @csrf
                  <button type="submit" class="btn-action btn-action-danger" title="Archivar">
                    <i class="bi bi-archive"></i>
                  </button>
                </form>
              @else
                <form method="POST" action="{{ route('postulantes.reactivar', $postulante) }}" class="d-inline">
                  @csrf
                  <button type="submit" class="btn-action btn-action-success" title="Reactivar">
                    <i class="bi bi-arrow-counterclockwise"></i>
                  </button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="text-center py-4 text-muted">No se encontraron postulantes con esos criterios.</td></tr>
        @endforelse
      </tbody>
    </table>
    </div>
  </div>
</div>

@if($postulantes->hasPages())
  <div class="mt-3 d-flex justify-content-center">
    {{ $postulantes->links() }}
  </div>
@endif

@endsection
