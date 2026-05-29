@extends('layouts.base')

@section('titulo', 'Periodos')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-calendar3 me-2"></i>Periodos Académicos</h1>
    <p class="page-subtitle">Gestión de periodos del Curso Preuniversitario</p>
  </div>
  <a href="{{ route('periodos.create') }}" class="btn btn-cup-primary">
    <i class="bi bi-plus-circle me-1"></i> Nuevo Periodo
  </a>
</div>

<x-buscador-cup
  :q="$q ?? ''"
  :estado="$estado ?? 'todos'"
  placeholder="Buscar por año o fecha (dd/mm/aaaa)..."
/>

<div class="panel-cup">
  <div class="panel-cup-body p-0">
    <div class="table-responsive">
    <table class="table-cup table mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Inicio Inscripción</th>
          <th>Fin Inscripción</th>
          <th>Inicio Curso</th>
          <th>Fin Curso</th>
          <th>Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($periodos as $periodo)
          <tr>
            <td>{{ $periodos->firstItem() + $loop->index }}</td>
            <td>{{ $periodo->fecha_ini_inscripcion->format('d/m/Y') }}</td>
            <td>{{ $periodo->fecha_fin_inscripcion->format('d/m/Y') }}</td>
            <td>{{ $periodo->fecha_ini_curso->format('d/m/Y') }}</td>
            <td>{{ $periodo->fecha_fin_curso->format('d/m/Y') }}</td>
            <td>
              @if($periodo->activo)
                <span class="badge-cup badge-activo">Activo</span>
              @else
                <span class="badge-cup badge-inactivo">Inactivo</span>
              @endif
            </td>
            <td class="text-end">
              <a href="{{ route('periodos.edit', $periodo) }}" class="btn-action btn-action-edit" title="Editar">
                <i class="bi bi-pencil"></i>
              </a>
              @if($periodo->activo)
                <form id="form-archivar-periodo-{{ $periodo->id }}"
                      action="{{ route('periodos.archivar', $periodo) }}" method="POST" style="display:inline">
                  @csrf
                  <button type="button" class="btn-action btn-action-danger" title="Archivar"
                          onclick="cupConfirmar({
                            titulo: 'Archivar periodo',
                            mensaje: '¿Querés archivar este periodo académico?',
                            subtexto: 'Quedará inactivo. Las inscripciones existentes no se eliminan. Podés reactivarlo después.',
                            textoBoton: 'Sí, archivar',
                            tipo: 'warning',
                            formSelector: '#form-archivar-periodo-{{ $periodo->id }}'
                          })">
                    <i class="bi bi-archive"></i>
                  </button>
                </form>
              @else
                <form id="form-reactivar-periodo-{{ $periodo->id }}"
                      action="{{ route('periodos.reactivar', $periodo) }}" method="POST" style="display:inline">
                  @csrf
                  <button type="button" class="btn-action btn-action-success" title="Reactivar"
                          onclick="cupConfirmar({
                            titulo: 'Reactivar periodo',
                            mensaje: '¿Querés reactivar este periodo académico?',
                            subtexto: 'Se desactivarán los demás periodos activos (solo puede haber uno activo).',
                            textoBoton: 'Sí, reactivar',
                            tipo: 'success',
                            formSelector: '#form-reactivar-periodo-{{ $periodo->id }}'
                          })">
                    <i class="bi bi-arrow-counterclockwise"></i>
                  </button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center py-4 text-muted">No se encontraron periodos.</td></tr>
        @endforelse
      </tbody>
    </table>
    </div>
  </div>
</div>

@if($periodos->hasPages())
  <div class="mt-3 d-flex justify-content-center">
    {{ $periodos->links() }}
  </div>
@endif

@endsection
