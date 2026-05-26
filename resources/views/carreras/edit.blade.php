<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Carrera</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4" style="max-width:600px">
    <h2>Editar Carrera</h2>
    <a href="{{ route('carreras.index') }}" class="btn btn-secondary mb-3">← Volver</a>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('carreras.update', $carrera) }}" method="POST" class="card p-4 bg-white">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Código</label>
            <input type="text" name="codigo" class="form-control text-uppercase"
                   value="{{ $carrera->codigo }}" required maxlength="20">
        </div>
        <div class="mb-3">
            <label class="form-label">Nombre de la Carrera</label>
            <input type="text" name="nombre" class="form-control"
                   value="{{ $carrera->nombre }}" required maxlength="150">
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3">{{ $carrera->descripcion }}</textarea>
        </div>

        @if($periodoActivo)
        <hr>
        <h6 class="text-primary">📊 Cupo para el periodo activo</h6>
        <div class="mb-3">
            <label class="form-label">Cupo máximo de admitidos</label>
            <input type="number" name="cupo_max" class="form-control"
                   value="{{ $cupoActual->cupo_max ?? old('cupo_max') }}" required min="1">
        </div>
        <div class="mb-3">
            <label class="form-label">Fecha COFI <small class="text-muted">(opcional)</small></label>
            <input type="date" name="fecha_cofi" class="form-control"
                   value="{{ $cupoActual?->fecha_cofi?->format('Y-m-d') ?? '' }}">
        </div>
        @else
        <div class="alert alert-warning">Sin periodo activo — no se puede editar el cupo.</div>
        @endif

        <button type="submit" class="btn btn-success w-100">Actualizar Carrera</button>
    </form>
</div>
</body>
</html>