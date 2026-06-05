@extends('layouts.base')

@section('titulo', 'Editar Horario')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-clock me-2"></i>Editar Horario</h1>
    <p class="page-subtitle">{{ $horario->codigo }} — {{ $horario->turno }} {{ $horario->rango }}</p>
  </div>
  <a href="{{ route('horarios.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

@if($errors->any())
  <div class="alert alert-cup-danger">
    <i class="bi bi-exclamation-triangle me-2"></i>
    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
  </div>
@endif

<div class="panel-cup" style="max-width:760px">
  <div class="panel-cup-body">
    <form action="{{ route('horarios.update', $horario) }}" method="POST">
      @csrf @method('PUT')
      @include('horarios._form', ['horario' => $horario])
      <hr>
      <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('horarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-cup-primary"><i class="bi bi-check-circle me-1"></i> Actualizar Horario</button>
      </div>
    </form>
  </div>
</div>

@endsection
