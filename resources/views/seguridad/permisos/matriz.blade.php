@extends('layouts.base')

@section('titulo', 'Matriz Rol-Permiso')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-grid-3x3 me-2"></i>Matriz Rol-Permiso</h1>
    <p class="page-subtitle">Vista de auditoría. Para modificar las asignaciones, editá el rol.</p>
  </div>
  <a href="{{ route('permisos.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver al Catálogo
  </a>
</div>

@php
  // Mapa rol_id => [permiso_id => true] para lookup rápido
  $mapa = [];
  foreach($roles as $rol) {
      $mapa[$rol->id] = $rol->permisos->pluck('id')->flip();
  }
  $moduloPrevio = null;
@endphp

<div class="panel-cup">
  <div class="panel-cup-body p-0" style="overflow-x:auto">
    <table class="table-cup table mb-0">
      <thead>
        <tr>
          <th>Módulo / Permiso</th>
          @foreach($roles as $rol)
            <th class="text-center">{{ $rol->nombre }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach($permisos as $permiso)
          @if($permiso->modulo !== $moduloPrevio)
            <tr>
              <td colspan="{{ $roles->count() + 1 }}" style="background:rgba(30,95,168,0.06)">
                <strong style="color:var(--cup-primary)">{{ $permiso->modulo }}</strong>
              </td>
            </tr>
            @php $moduloPrevio = $permiso->modulo; @endphp
          @endif
          <tr>
            <td><strong>{{ $permiso->codigo }}</strong></td>
            @foreach($roles as $rol)
              <td class="text-center">
                @if(isset($mapa[$rol->id][$permiso->id]))
                  <i class="bi bi-check-circle-fill text-success"></i>
                @else
                  <span class="text-muted">·</span>
                @endif
              </td>
            @endforeach
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection
