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

<div class="panel-cup">
  <div class="panel-cup-body p-0">
    <div class="table-responsive">
    <table class="table-cup table mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Sigla</th>
          <th>Nombre</th>
          <th>Días</th>
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
            <td>{{ $loop->iteration }}</td>
            <td><span class="badge-cup badge-modulo">{{ $materia->sigla }}</span></td>
            <td><strong>{{ $materia->nombre }}</strong></td>
            <td><span class="badge-cup badge-modulo">{{ $materia->dias }}</span></td>
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
                <form action="{{ route('materias.destroy', $materia) }}" method="POST" style="display:inline"
                      onsubmit="return confirm('¿Desactivar esta materia?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn-action btn-action-danger" title="Desactivar">
                    <i class="bi bi-archive"></i>
                  </button>
                </form>
              @else
                <form action="{{ route('materias.reactivar', $materia) }}" method="POST" style="display:inline"
                      onsubmit="return confirm('¿Reactivar esta materia?')">
                  @csrf
                  <button type="submit" class="btn-action btn-action-success" title="Reactivar">
                    <i class="bi bi-arrow-counterclockwise"></i>
                  </button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="10" class="text-center py-4 text-muted">No hay materias registradas.</td></tr>
        @endforelse
      </tbody>
    </table>
    </div>
  </div>
</div>

@endsection
