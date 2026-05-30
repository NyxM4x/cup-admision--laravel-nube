@extends('layouts.base')

@section('titulo', 'Carreras')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-mortarboard-fill me-2"></i>Carreras del CUP</h1>
    <p class="page-subtitle">Carreras disponibles y su cupo en el periodo activo</p>
  </div>
  <a href="{{ route('carreras.create') }}" class="btn btn-cup-primary">
    <i class="bi bi-plus-circle me-1"></i> Nueva Carrera
  </a>
</div>

@if(!$periodoActivo)
  <div class="alert alert-warning border-0" style="border-radius:8px">
    <i class="bi bi-exclamation-triangle me-2"></i>No hay un periodo académico activo. Las carreras no pueden asociarse a ningún periodo.
    <a href="{{ route('periodos.create') }}" class="alert-link">Crear periodo</a>
  </div>
@else
  <div class="alert alert-info border-0" style="border-radius:8px">
    <i class="bi bi-calendar-event me-2"></i>Periodo activo:
    <strong>{{ $periodoActivo->fecha_ini_inscripcion->format('d/m/Y') }} — {{ $periodoActivo->fecha_fin_curso->format('d/m/Y') }}</strong>
  </div>
@endif

@if($errors->has('general'))
  <div class="alert alert-cup-danger">
    <i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first('general') }}
  </div>
@endif

<x-buscador-cup
  :q="$q ?? ''"
  :estado="$estado ?? 'activos'"
  placeholder="Buscar por código o nombre..."
/>

<div class="panel-cup">
  <div class="panel-cup-body p-0">
    <div class="table-responsive">
    <table class="table-cup table mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Código</th>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Cupo (periodo activo)</th>
          <th>Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($carreras as $carrera)
          <tr>
            <td>{{ $carreras->firstItem() + $loop->index }}</td>
            <td><span class="badge-cup badge-modulo">{{ $carrera->codigo }}</span></td>
            <td><strong>{{ $carrera->nombre }}</strong></td>
            <td>{{ $carrera->descripcion ?? '—' }}</td>
            <td>
              @if($carrera->cupoActivo)
                <span class="badge-cup badge-modulo">{{ $carrera->cupoActivo->cupo_max }}</span>
              @else
                <span class="text-muted">Sin cupo asignado</span>
              @endif
            </td>
            <td>
              @if($carrera->activo)
                <span class="badge-cup badge-activo">Activa</span>
              @else
                <span class="badge-cup badge-inactivo">Inactiva</span>
              @endif
            </td>
            <td class="text-end">
              <a href="{{ route('carreras.edit', $carrera) }}" class="btn-action btn-action-edit" title="Editar">
                <i class="bi bi-pencil"></i>
              </a>
              @if($carrera->activo)
                <form id="form-archivar-carrera-{{ $carrera->id }}"
                      action="{{ route('carreras.archivar', $carrera) }}" method="POST" style="display:inline">
                  @csrf
                  <button type="button" class="btn-action btn-action-danger" title="Archivar"
                          onclick="cupConfirmar({
                            titulo: 'Archivar carrera',
                            mensaje: '¿Querés archivar la carrera {{ addslashes($carrera->nombre) }}?',
                            subtexto: 'Quedará inactiva. No se elimina; podés reactivarla después.',
                            textoBoton: 'Sí, archivar',
                            tipo: 'warning',
                            formSelector: '#form-archivar-carrera-{{ $carrera->id }}'
                          })">
                    <i class="bi bi-archive"></i>
                  </button>
                </form>
              @else
                <form id="form-reactivar-carrera-{{ $carrera->id }}"
                      action="{{ route('carreras.reactivar', $carrera) }}" method="POST" style="display:inline">
                  @csrf
                  <button type="button" class="btn-action btn-action-success" title="Reactivar"
                          onclick="cupConfirmar({
                            titulo: 'Reactivar carrera',
                            mensaje: '¿Querés reactivar la carrera {{ addslashes($carrera->nombre) }}?',
                            textoBoton: 'Sí, reactivar',
                            tipo: 'success',
                            formSelector: '#form-reactivar-carrera-{{ $carrera->id }}'
                          })">
                    <i class="bi bi-arrow-counterclockwise"></i>
                  </button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center py-4 text-muted">No se encontraron carreras.</td></tr>
        @endforelse
      </tbody>
    </table>
    </div>
  </div>
</div>

@if($carreras->hasPages())
  <div class="mt-3 d-flex justify-content-center">
    {{ $carreras->links() }}
  </div>
@endif

@endsection
