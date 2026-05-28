@extends('layouts.base')

@section('titulo', 'Detalle del Postulante')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-person-vcard me-2"></i>Detalle del Postulante</h1>
    <p class="page-subtitle">{{ $postulante->persona->nombre }}</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('postulantes.edit', $postulante) }}" class="btn btn-cup-primary">
      <i class="bi bi-pencil me-1"></i> Editar
    </a>
    <a href="{{ route('postulantes.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
  </div>
</div>

@php $badges = ['pendiente'=>'badge-warning-cup','inscrito'=>'badge-modulo','aprobado'=>'badge-activo','reprobado'=>'badge-inactivo']; @endphp

<div class="row g-3">
  <div class="col-lg-8">
    <div class="panel-cup">
      <div class="panel-cup-header">
        <strong><i class="bi bi-clipboard me-1"></i> Datos Personales</strong>
        <span class="badge-cup {{ $badges[$postulante->estado] ?? 'badge-modulo' }}">{{ ucfirst($postulante->estado) }}</span>
      </div>
      <div class="panel-cup-body">
        <table class="table mb-0">
          <tbody>
            <tr><th style="width:200px;color:var(--cup-muted);font-weight:500;font-size:0.85rem;">CI</th><td>{{ $postulante->persona->ci }}</td></tr>
            <tr><th style="color:var(--cup-muted);font-weight:500;font-size:0.85rem;">Nombre</th><td><strong>{{ $postulante->persona->nombre }}</strong></td></tr>
            <tr><th style="color:var(--cup-muted);font-weight:500;font-size:0.85rem;">Fecha nacimiento</th><td>{{ optional($postulante->persona->fecha_nacimiento)->format('d/m/Y') ?? '—' }}</td></tr>
            <tr><th style="color:var(--cup-muted);font-weight:500;font-size:0.85rem;">Sexo</th><td>{{ $postulante->persona->sexo == 'M' ? 'Masculino' : ($postulante->persona->sexo == 'F' ? 'Femenino' : '—') }}</td></tr>
            <tr><th style="color:var(--cup-muted);font-weight:500;font-size:0.85rem;">Dirección</th><td>{{ $postulante->persona->direccion ?? '—' }}</td></tr>
            <tr><th style="color:var(--cup-muted);font-weight:500;font-size:0.85rem;">Teléfono</th><td>{{ $postulante->persona->telefono ?? '—' }}</td></tr>
            <tr><th style="color:var(--cup-muted);font-weight:500;font-size:0.85rem;">Correo</th><td>{{ $postulante->persona->correo ?? '—' }}</td></tr>
            <tr><th style="color:var(--cup-muted);font-weight:500;font-size:0.85rem;">Colegio</th><td>{{ $postulante->colegio }}</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    @forelse($postulante->inscripciones as $inscripcion)
      <div class="panel-cup mb-3">
        <div class="panel-cup-header">
          <strong><i class="bi bi-mortarboard me-1"></i> Inscripción {{ $inscripcion->periodo->fecha_ini_inscripcion->format('Y') }}</strong>
        </div>
        <div class="panel-cup-body">
          <p class="mb-1"><small class="text-muted">Fecha:</small> {{ $inscripcion->fecha_inscripcion->format('d/m/Y') }}</p>
          <p class="mb-3"><small class="text-muted">Estado:</small> {{ ucfirst($inscripcion->estado) }}</p>
          <small class="text-muted d-block mb-2">Carreras postuladas:</small>
          @foreach($inscripcion->postulacionCarreras as $pc)
            <div class="mb-1">
              <span class="badge-cup badge-modulo">Opción {{ $pc->prioridad }}</span>
              {{ $pc->carrera->codigo }} — {{ $pc->carrera->nombre }}
            </div>
          @endforeach
        </div>
      </div>
    @empty
      <div class="panel-cup">
        <div class="panel-cup-body text-center text-muted">
          <i class="bi bi-inbox d-block mb-2 fs-3"></i>Sin inscripciones registradas.
        </div>
      </div>
    @endforelse
  </div>
</div>

@endsection
