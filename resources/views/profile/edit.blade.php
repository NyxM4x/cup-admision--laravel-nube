@extends('layouts.base')

@section('titulo', 'Mi Perfil')

@section('contenido')

<div class="page-header mb-4">
  <h1><i class="bi bi-person-circle me-2"></i>Mi Perfil</h1>
  <p class="page-subtitle">Gestioná tu información personal, contraseña y seguridad de cuenta</p>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="panel-cup mb-3">
      <div class="panel-cup-header">
        <strong><i class="bi bi-person-vcard me-1"></i> Información Personal</strong>
      </div>
      <div class="panel-cup-body">
        @include('profile.partials.update-profile-information-form')
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="panel-cup mb-3">
      <div class="panel-cup-header">
        <strong><i class="bi bi-shield-lock me-1"></i> Cambiar Contraseña</strong>
      </div>
      <div class="panel-cup-body">
        @include('profile.partials.update-password-form')
      </div>
    </div>

    <div class="panel-cup">
      <div class="panel-cup-header">
        <strong style="color: var(--cup-danger);">
          <i class="bi bi-exclamation-triangle me-1"></i> Zona de Peligro
        </strong>
      </div>
      <div class="panel-cup-body">
        @include('profile.partials.delete-user-form')
      </div>
    </div>
  </div>
</div>

@endsection
