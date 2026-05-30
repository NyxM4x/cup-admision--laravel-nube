@extends('layouts.base')

@section('titulo', 'Materias')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-book me-2"></i>Materias del CUP</h1>
    <p class="page-subtitle">Materias y su configuración de evaluación</p>
  </div>
  <a href="{{ route('materias.create') }}" class="btn btn-cup-primary">
    <i class="bi bi-plus-circle me-1"></i> Nueva Materia
  </a>
</div>

<x-buscador-cup
  :q="$q ?? ''"
  :estado="$estado ?? 'activos'"
  placeholder="Buscar por sigla o nombre..."
/>

<div class="panel-cup">
  <div class="panel-cup-body p-0">
    <div class="table-responsive">
    <table class="table-cup table mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Sigla</th>
          <th>Nombre</th>
          <th class="text-center">Exámenes</th>
          <th class="text-center">Peso E1</th>
          <th class="text-center">Peso E2</th>
          <th class="text-center">Peso E3</th>
          <th>Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($materias as $materia)
          <tr>
            <td>{{ $materias->firstItem() + $loop->index }}</td>
            <td><span class="badge-cup badge-modulo">{{ $materia->sigla }}</span></td>
            <td><strong>{{ $materia->nombre }}</strong></td>
            <td class="text-center">{{ $materia->cant_examenes }}</td>
            <td class="text-center">{{ $materia->peso_examen1 }}%</td>
            <td class="text-center">{{ $materia->peso_examen2 }}%</td>
            <td class="text-center">{{ $materia->peso_examen3 }}%</td>
            <td>
              @if($materia->activo)
                <span class="badge-cup badge-activo">Activa</span>
              @else
                <span class="badge-cup badge-inactivo">Inactiva</span>
              @endif
            </td>
            <td class="text-end">
              <a href="{{ route('materias.edit', $materia) }}" class="btn-action btn-action-edit" title="Editar">
                <i class="bi bi-pencil"></i>
              </a>
              @if($materia->activo)
                <form id="form-archivar-materia-{{ $materia->id }}"
                      action="{{ route('materias.archivar', $materia) }}" method="POST" style="display:inline">
                  @csrf
                  <button type="button" class="btn-action btn-action-danger" title="Archivar"
                          onclick="cupConfirmar({
                            titulo: 'Archivar materia',
                            mensaje: '¿Querés archivar la materia {{ addslashes($materia->nombre) }}?',
                            subtexto: 'Quedará inactiva. No se elimina; podés reactivarla después.',
                            textoBoton: 'Sí, archivar',
                            tipo: 'warning',
                            formSelector: '#form-archivar-materia-{{ $materia->id }}'
                          })">
                    <i class="bi bi-archive"></i>
                  </button>
                </form>
              @else
                <form id="form-reactivar-materia-{{ $materia->id }}"
                      action="{{ route('materias.reactivar', $materia) }}" method="POST" style="display:inline">
                  @csrf
                  <button type="button" class="btn-action btn-action-success" title="Reactivar"
                          onclick="cupConfirmar({
                            titulo: 'Reactivar materia',
                            mensaje: '¿Querés reactivar la materia {{ addslashes($materia->nombre) }}?',
                            textoBoton: 'Sí, reactivar',
                            tipo: 'success',
                            formSelector: '#form-reactivar-materia-{{ $materia->id }}'
                          })">
                    <i class="bi bi-arrow-counterclockwise"></i>
                  </button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="9" class="text-center py-4 text-muted">No se encontraron materias.</td></tr>
        @endforelse
      </tbody>
    </table>
    </div>
  </div>
</div>

@if($materias->hasPages())
  <div class="mt-3 d-flex justify-content-center">
    {{ $materias->links() }}
  </div>
@endif

@endsection
