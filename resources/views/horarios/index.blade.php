@extends('layouts.base')

@section('titulo', 'Horarios')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-clock me-2"></i>Horarios</h1>
    <p class="page-subtitle">Bloques horarios para la asignación de grupos</p>
  </div>
  <a href="{{ route('horarios.create') }}" class="btn btn-cup-primary">
    <i class="bi bi-plus-circle me-1"></i> Nuevo Horario
  </a>
</div>

<x-buscador-cup
  :q="$q ?? ''"
  :estado="$estado ?? 'todos'"
  placeholder="Buscar por código, turno o días..."
/>

<div class="panel-cup">
  <div class="panel-cup-body p-0">
    <div class="table-responsive">
    <table class="table-cup table mb-0">
      <thead>
        <tr>
          <th>Código</th>
          <th>Turno</th>
          <th>Días</th>
          <th>Horario</th>
          <th>Descripción</th>
          <th>Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($horarios as $horario)
          <tr>
            <td><span class="badge-cup badge-modulo">{{ $horario->codigo }}</span></td>
            <td>{{ $horario->turno }}</td>
            <td><small>{{ $horario->dias }}</small></td>
            <td>{{ $horario->rango }}</td>
            <td><small class="text-muted">{{ $horario->descripcion ?? '—' }}</small></td>
            <td>
              @if($horario->activo)
                <span class="badge-cup badge-activo">Activo</span>
              @else
                <span class="badge-cup badge-inactivo">Inactivo</span>
              @endif
            </td>
            <td class="text-end">
              <a href="{{ route('horarios.edit', $horario) }}" class="btn-action btn-action-edit" title="Editar">
                <i class="bi bi-pencil"></i>
              </a>
              @if($horario->activo)
                <form id="form-archivar-horario-{{ $horario->id }}"
                      action="{{ route('horarios.archivar', $horario) }}" method="POST" style="display:inline">
                  @csrf
                  <button type="button" class="btn-action btn-action-danger" title="Archivar"
                          onclick="cupConfirmar({
                            titulo: 'Archivar horario',
                            mensaje: '¿Querés archivar el horario {{ addslashes($horario->codigo) }}?',
                            subtexto: 'No se elimina; podés reactivarlo después.',
                            textoBoton: 'Sí, archivar',
                            tipo: 'warning',
                            formSelector: '#form-archivar-horario-{{ $horario->id }}'
                          })">
                    <i class="bi bi-archive"></i>
                  </button>
                </form>
              @else
                <form id="form-reactivar-horario-{{ $horario->id }}"
                      action="{{ route('horarios.reactivar', $horario) }}" method="POST" style="display:inline">
                  @csrf
                  <button type="button" class="btn-action btn-action-success" title="Reactivar"
                          onclick="cupConfirmar({
                            titulo: 'Reactivar horario',
                            mensaje: '¿Querés reactivar el horario {{ addslashes($horario->codigo) }}?',
                            textoBoton: 'Sí, reactivar',
                            tipo: 'success',
                            formSelector: '#form-reactivar-horario-{{ $horario->id }}'
                          })">
                    <i class="bi bi-arrow-counterclockwise"></i>
                  </button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center py-4 text-muted">No se encontraron horarios.</td></tr>
        @endforelse
      </tbody>
    </table>
    </div>
  </div>
</div>

@if($horarios->hasPages())
  <div class="mt-3 d-flex justify-content-center">
    {{ $horarios->links('pagination::bootstrap-5') }}
  </div>
@endif

@endsection

