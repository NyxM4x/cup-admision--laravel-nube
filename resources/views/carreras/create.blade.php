<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Carrera</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4" style="max-width:600px">
    <h2>Nueva Carrera</h2>
    <a href="{{ route('carreras.index') }}" class="btn btn-secondary mb-3">← Volver</a>

    @if(!$periodoActivo)
        <div class="alert alert-danger">
            ❌ No hay periodo activo. No puedes registrar carreras sin un periodo activo.
            <a href="{{ route('periodos.create') }}" class="alert-link">Crear periodo primero</a>
        </div>
    @else
        <div class="alert alert-info">
            📅 Se asociará al periodo activo: <strong>{{ $periodoActivo->fecha_ini_inscripcion->format('d/m/Y') }}</strong>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('carreras.store') }}" method="POST" class="card p-4 bg-white">
        @csrf
        <div class="mb-3">
            <label class="form-label">Código <small class="text-muted">(ej: ING-COMP)</small></label>
            <input type="text" name="codigo" class="form-control text-uppercase"
                   value="{{ old('codigo') }}" required maxlength="20">
        </div>
        <div class="mb-3">
            <label class="form-label">Nombre de la Carrera</label>
            <input type="text" name="nombre" class="form-control"
                   value="{{ old('nombre') }}" required maxlength="150">
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción <small class="text-muted">(opcional)</small></label>
            <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
        </div>
        <hr>
        <h6 class="text-primary">📊 Cupo para el periodo activo</h6>
        <div class="mb-3">
            <label class="form-label">Cupo máximo de admitidos</label>
            <input type="number" name="cupo_max" class="form-control"
                   value="{{ old('cupo_max') }}" required min="1">
            <small class="text-muted">Este valor es el INPUT del algoritmo de ranking (CU23/CU24)</small>
        </div>
        <div class="mb-3">
            <label class="form-label">Fecha COFI <small class="text-muted">(opcional)</small></label>
            <input type="date" name="fecha_cofi" class="form-control" value="{{ old('fecha_cofi') }}">
        </div>
        <button type="submit" class="btn btn-primary w-100" {{ !$periodoActivo ? 'disabled' : '' }}>
            Guardar Carrera
        </button>
    </form>
</div>
</body>
</html>