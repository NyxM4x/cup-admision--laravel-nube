@extends('layouts.base')

@section('titulo', 'Requisitos')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-list-check me-2"></i>Requisitos de Inscripción</h1>
    <p class="page-subtitle">Documentos requeridos para la inscripción de postulantes</p>
  </div>
  <a href="{{ route('requisitos.create') }}" class="btn btn-cup-primary">
    <i class="bi bi-plus-circle me-1"></i> Nuevo Requisito
  </a>
</div>

@if(!$periodoActivo)
  <div class="alert alert-warning border-0" style="border-radius:8px">
    <i class="bi bi-exclamation-triangle me-2"></i>No hay periodo activo.
    <a href="{{ route('periodos.create') }}" class="alert-link">Crear periodo</a>
  </div>
@else
  <div class="alert alert-info border-0" style="border-radius:8px">
    <i class="bi bi-calendar-event me-2"></i>Periodo activo:
    <strong>{{ $periodoActivo->fecha_ini_inscripcion->format('d/m/Y') }} — {{ $periodoActivo->fecha_fin_curso->format('d/m/Y') }}</strong>
  </div>
@endif

<x-buscador-cup
  :q="$q ?? ''"
  :estado="$estado ?? 'todos'"
  placeholder="Buscar por nombre del requisito..."
/>

<div class="panel-cup">
  <div class="panel-cup-body p-0">
    <div class="table-responsive">
    <table class="table-cup table mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Obligatorio</th>
          <th>Formatos</th>
          <th>Tamaño máx.</th>
          <th>Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($requisitos as $req)
          <tr>
            <td>{{ $requisitos->firstItem() + $loop->index }}</td>
            <td><strong>{{ $req->nombre }}</strong></td>
            <td>{{ $req->descripcion ?? '—' }}</td>
            <td>
              @if($req->obligatorio)
                <span class="badge bg-danger">Obligatorio</span>
              @else
                <span class="badge bg-secondary">Opcional</span>
              @endif
            </td>
            <td><code>{{ $req->formato_aceptado }}</code></td>
            <td>{{ number_format($req->tamanio_max_kb / 1024, 1) }} MB</td>
            <td>
              @if($req->activo)
                <span class="badge-cup badge-activo">Activo</span>
              @else
                <span class="badge-cup badge-inactivo">Inactivo</span>
              @endif
            </td>
            <td class="text-end">
              <a href="{{ route('requisitos.edit', $req) }}" class="btn-action btn-action-edit" title="Editar">
                <i class="bi bi-pencil"></i>
              </a>
              @if($req->activo)
                <form id="form-archivar-requisito-{{ $req->id }}"
                      action="{{ route('requisitos.archivar', $req) }}" method="POST" style="display:inline">
                  @csrf
                  <button type="button" class="btn-action btn-action-danger" title="Archivar"
                          onclick="cupConfirmar({
                            titulo: 'Archivar requisito',
                            mensaje: '¿Querés archivar el requisito {{ addslashes($req->nombre) }}?',
                            subtexto: 'Quedará inactivo. No se elimina; podés reactivarlo después.',
                            textoBoton: 'Sí, archivar',
                            tipo: 'warning',
                            formSelector: '#form-archivar-requisito-{{ $req->id }}'
                          })">
                    <i class="bi bi-archive"></i>
                  </button>
                </form>
              @else
                <form id="form-reactivar-requisito-{{ $req->id }}"
                      action="{{ route('requisitos.reactivar', $req) }}" method="POST" style="display:inline">
                  @csrf
                  <button type="button" class="btn-action btn-action-success" title="Reactivar"
                          onclick="cupConfirmar({
                            titulo: 'Reactivar requisito',
                            mensaje: '¿Querés reactivar el requisito {{ addslashes($req->nombre) }}?',
                            textoBoton: 'Sí, reactivar',
                            tipo: 'success',
                            formSelector: '#form-reactivar-requisito-{{ $req->id }}'
                          })">
                    <i class="bi bi-arrow-counterclockwise"></i>
                  </button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="text-center py-4 text-muted">No se encontraron requisitos.</td></tr>
        @endforelse
      </tbody>
    </table>
    </div>
  </div>
</div>

@if($requisitos->hasPages())
  <div class="mt-3 d-flex justify-content-center">
    {{ $requisitos->links() }}
  </div>
@endif

@endsection
