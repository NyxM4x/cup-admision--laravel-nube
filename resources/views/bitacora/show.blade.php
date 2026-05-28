@extends('layouts.base')

@section('titulo', 'Detalle de Registro #' . $registro->id)

@section('contenido')

@php
  $accion = $registro->accion;
  $verdes = ['LOGIN_OK','USUARIO_CREADO','ROL_CREADO','AULA_CREADA','PERIODO_CREADO',
             'CARRERA_CREADA','MATERIA_CREADA','DOCENTE_CREADO','POSTULANTE_CREADO',
             'USUARIO_REACTIVADO','ROL_REACTIVADO','AULA_REACTIVADA','CREAR','ACTIVAR'];
  $rojos = ['LOGIN_FAIL','LOGIN_INACTIVO','ACCESO_DENEGADO','USUARIO_ELIMINADO',
            'ROL_ELIMINADO','AULA_ELIMINADA','ELIMINAR'];
  $amarillos = ['USUARIO_INACTIVADO','ROL_INACTIVADO','AULA_INACTIVADA','LOGOUT_OK',
                'USUARIO_EDITADO','ROL_EDITADO','AULA_EDITADA','EDITAR','INACTIVAR'];
  if (in_array($accion, $verdes))         $clase = 'badge-activo';
  elseif (in_array($accion, $rojos))      $clase = 'badge-inactivo';
  elseif (in_array($accion, $amarillos))  $clase = 'badge-warning-cup';
  else                                    $clase = 'badge-modulo';
@endphp

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-file-earmark-text me-2"></i>Detalle de Registro #{{ $registro->id }}</h1>
    <p class="page-subtitle">Información completa del evento registrado en la bitácora</p>
  </div>
  <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('bitacora.index') }}"
     class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="panel-cup">
      <div class="panel-cup-header">
        <strong><i class="bi bi-info-circle me-1"></i> Información del evento</strong>
        <span class="badge-cup {{ $clase }}">{{ $registro->accion }}</span>
      </div>
      <div class="panel-cup-body">
        <table class="table mb-0">
          <tbody>
            <tr>
              <th style="width:200px;color:var(--cup-muted);font-weight:500;font-size:0.85rem;">ID</th>
              <td><strong>#{{ $registro->id }}</strong></td>
            </tr>
            <tr>
              <th style="color:var(--cup-muted);font-weight:500;font-size:0.85rem;">
                <i class="bi bi-clock me-1"></i> Fecha y hora
              </th>
              <td>{{ $registro->created_at?->format('d/m/Y H:i:s') }}</td>
            </tr>
            <tr>
              <th style="color:var(--cup-muted);font-weight:500;font-size:0.85rem;">
                <i class="bi bi-person me-1"></i> Usuario
              </th>
              <td>
                @if($registro->user)
                  <strong>{{ $registro->user->name }}</strong>
                  <br>
                  <small class="text-muted">{{ $registro->user->email }} · ID {{ $registro->user->id }}</small>
                @else
                  <em class="text-muted">— Sistema / Anónimo —</em>
                @endif
              </td>
            </tr>
            <tr>
              <th style="color:var(--cup-muted);font-weight:500;font-size:0.85rem;">
                <i class="bi bi-tag me-1"></i> Módulo
              </th>
              <td><span class="badge-cup badge-modulo">{{ $registro->modulo }}</span></td>
            </tr>
            <tr>
              <th style="color:var(--cup-muted);font-weight:500;font-size:0.85rem;">
                <i class="bi bi-lightning me-1"></i> Acción
              </th>
              <td><span class="badge-cup {{ $clase }}">{{ $registro->accion }}</span></td>
            </tr>
            <tr>
              <th style="color:var(--cup-muted);font-weight:500;font-size:0.85rem;">
                <i class="bi bi-card-text me-1"></i> Descripción
              </th>
              <td>{{ $registro->descripcion }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="panel-cup">
      <div class="panel-cup-header">
        <strong><i class="bi bi-hdd-network me-1"></i> Datos técnicos</strong>
      </div>
      <div class="panel-cup-body">
        <div class="mb-3">
          <small class="text-muted d-block mb-1">
            <i class="bi bi-globe me-1"></i> Dirección IP
          </small>
          <strong>{{ $registro->ip ?? '—' }}</strong>
        </div>
        <div>
          <small class="text-muted d-block mb-1">
            <i class="bi bi-window me-1"></i> User Agent
          </small>
          <small style="word-break:break-all;color:var(--cup-text);font-family:monospace;font-size:0.78rem;">
            {{ $registro->user_agent ?? '—' }}
          </small>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('styles')
<style>
  .badge-warning-cup {
    background: rgba(245,158,11,0.12);
    color: #b45309;
    border: 1px solid rgba(245,158,11,0.30);
  }
</style>
@endpush
