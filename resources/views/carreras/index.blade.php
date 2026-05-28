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
            <td>{{ $loop->iteration }}</td>
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
                <form action="{{ route('carreras.destroy', $carrera) }}" method="POST" style="display:inline"
                      onsubmit="return confirm('¿Desactivar esta carrera?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn-action btn-action-danger" title="Desactivar">
                    <i class="bi bi-archive"></i>
                  </button>
                </form>
              @else
                <form action="{{ route('carreras.reactivar', $carrera) }}" method="POST" style="display:inline"
                      onsubmit="return confirm('¿Reactivar esta carrera?')">
                  @csrf
                  <button type="submit" class="btn-action btn-action-success" title="Reactivar">
                    <i class="bi bi-arrow-counterclockwise"></i>
                  </button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center py-4 text-muted">No hay carreras registradas.</td></tr>
        @endforelse
      </tbody>
    </table>
    </div>
  </div>
</div>

@endsection
