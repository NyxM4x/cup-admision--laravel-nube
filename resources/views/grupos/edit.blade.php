@extends('layouts.base')

@section('titulo', 'Editar Grupo')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-people-fill me-2"></i>Editar Grupo</h1>
    <p class="page-subtitle">{{ $grupo->codigo }} — {{ $grupo->materia->nombre ?? '' }}</p>
  </div>
  <a href="{{ route('grupos.index', ['periodo_id' => $grupo->periodo_id]) }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

@if($errors->any())
  <div class="alert alert-cup-danger">
    <i class="bi bi-exclamation-triangle me-2"></i>
    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
  </div>
@endif

<div class="panel-cup" style="max-width:860px">
  <div class="panel-cup-body">
    <form action="{{ route('grupos.update', $grupo) }}" method="POST">
      @csrf @method('PUT')
      @include('grupos._form', ['grupo' => $grupo])
      <hr>
      <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('grupos.index', ['periodo_id' => $grupo->periodo_id]) }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-cup-primary"><i class="bi bi-check-circle me-1"></i> Actualizar Grupo</button>
      </div>
    </form>
  </div>
</div>

@endsection
