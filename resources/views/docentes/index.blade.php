@extends('layouts.base')

@section('titulo', 'Docentes')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-person-workspace me-2"></i>Gestión de Docentes</h1>
    <p class="page-subtitle">Docentes registrados en el sistema</p>
  </div>
  <a href="{{ route('docentes.create') }}" class="btn btn-cup-primary">
    <i class="bi bi-plus-circle me-1"></i> Nuevo Docente
  </a>
</div>

<x-buscador-cup
  :q="$q ?? ''"
  :estado="$estado ?? 'activos'"
  placeholder="Buscar docente por nombre, CI o profesión..."
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
          <th>Teléfono</th>
          <th>Correo</th>
          <th>Profesión</th>
          <th>Exp.</th>
          <th>Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($docentes as $docente)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $docente->persona->ci }}</td>
            <td><strong>{{ $docente->persona->nombre }}</strong></td>
            <td>{{ $docente->persona->telefono ?? '—' }}</td>
            <td>{{ $docente->persona->correo ?? '—' }}</td>
            <td>{{ $docente->profesion->nombre ?? '—' }}</td>
            <td>{{ $docente->anios_experiencia }} años</td>
            <td>
              @if($docente->activo)
                <span class="badge-cup badge-activo">Activo</span>
              @else
                <span class="badge-cup badge-inactivo">Inactivo</span>
              @endif
            </td>
            <td class="text-end">
              <a href="{{ route('docentes.edit', $docente) }}" class="btn-action btn-action-edit" title="Editar">
                <i class="bi bi-pencil"></i>
              </a>
              @if($docente->activo)
                <form id="form-desactivar-docente-{{ $docente->id }}"
                      action="{{ route('docentes.destroy', $docente) }}" method="POST" style="display:inline">
                  @csrf @method('DELETE')
                  <button type="button" class="btn-action btn-action-danger" title="Desactivar"
                          onclick="cupConfirmar({
                            titulo: 'Desactivar docente',
                            mensaje: '¿Querés desactivar a {{ addslashes($docente->persona->nombre) }}?',
                            subtexto: 'No se eliminará; podés reactivarlo después.',
                            textoBoton: 'Sí, desactivar',
                            tipo: 'warning',
                            formSelector: '#form-desactivar-docente-{{ $docente->id }}'
                          })">
                    <i class="bi bi-archive"></i>
                  </button>
                </form>
              @else
                <form id="form-reactivar-docente-{{ $docente->id }}"
                      action="{{ route('docentes.reactivar', $docente) }}" method="POST" style="display:inline">
                  @csrf
                  <button type="button" class="btn-action btn-action-success" title="Reactivar"
                          onclick="cupConfirmar({
                            titulo: 'Reactivar docente',
                            mensaje: '¿Querés reactivar a {{ addslashes($docente->persona->nombre) }}?',
                            textoBoton: 'Sí, reactivar',
                            tipo: 'success',
                            formSelector: '#form-reactivar-docente-{{ $docente->id }}'
                          })">
                    <i class="bi bi-arrow-counterclockwise"></i>
                  </button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="9" class="text-center py-4 text-muted">No hay docentes registrados aún.</td></tr>
        @endforelse
      </tbody>
    </table>
    </div>
  </div>
</div>

@endsection
