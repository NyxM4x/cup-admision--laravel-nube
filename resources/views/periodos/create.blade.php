html<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Periodo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4" style="max-width:600px">
    <h2>Nuevo Periodo Académico</h2>
    <a href="{{ route('periodos.index') }}" class="btn btn-secondary mb-3">← Volver</a>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('periodos.store') }}" method="POST" class="card p-4 bg-white">
        @csrf
        <div class="mb-3">
            <label class="form-label">Inicio de Inscripción</label>
            <input type="date" name="fecha_ini_inscripcion" class="form-control" value="{{ old('fecha_ini_inscripcion') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Fin de Inscripción</label>
            <input type="date" name="fecha_fin_inscripcion" class="form-control" value="{{ old('fecha_fin_inscripcion') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Inicio del Curso</label>
            <input type="date" name="fecha_ini_curso" class="form-control" value="{{ old('fecha_ini_curso') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Fin del Curso</label>
            <input type="date" name="fecha_fin_curso" class="form-control" value="{{ old('fecha_fin_curso') }}" required>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="activo" class="form-check-input" id="activo">
            <label class="form-check-label" for="activo">Marcar como periodo activo</label>
        </div>
        <button type="submit" class="btn btn-primary w-100">Guardar Periodo</button>
    </form>
</div>
</body>
</html>
