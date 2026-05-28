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
            <td>{{ $loop->iteration }}</td>
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
              <form action="{{ route('periodos.destroy', $periodo) }}" method="POST" style="display:inline"
                    onsubmit="return confirm('¿Eliminar este periodo?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-action btn-action-danger" title="Eliminar">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center py-4 text-muted">No hay periodos registrados.</td></tr>
        @endforelse
      </tbody>
    </table>
    </div>
  </div>
</div>

@endsection
