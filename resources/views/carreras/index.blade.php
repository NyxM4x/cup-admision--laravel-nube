<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carreras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Carreras del CUP</h2>
        <a href="{{ route('carreras.create') }}" class="btn btn-primary">+ Nueva Carrera</a>
    </div>

    @if(!$periodoActivo)
        <div class="alert alert-warning">
            ⚠️ No hay un periodo académico activo. Las carreras no pueden asociarse a ningún periodo.
            <a href="{{ route('periodos.create') }}" class="alert-link">Crear periodo</a>
        </div>
    @else
        <div class="alert alert-info">
            📅 Periodo activo: <strong>{{ $periodoActivo->fecha_ini_inscripcion->format('d/m/Y') }} — {{ $periodoActivo->fecha_fin_curso->format('d/m/Y') }}</strong>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->has('general'))
        <div class="alert alert-danger">{{ $errors->first('general') }}</div>
    @endif

    <table class="table table-bordered table-striped bg-white">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Cupo (periodo activo)</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($carreras as $carrera)
            <tr>
                <td>{{ $carrera->id }}</td>
                <td><span class="badge bg-dark">{{ $carrera->codigo }}</span></td>
                <td>{{ $carrera->nombre }}</td>
                <td>{{ $carrera->descripcion ?? '—' }}</td>
                <td>
                    @if($carrera->cupoActivo)
                        <span class="badge bg-primary fs-6">{{ $carrera->cupoActivo->cupo_max }}</span>
                    @else
                        <span class="text-muted">Sin cupo asignado</span>
                    @endif
                </td>
                <td>
                    @if($carrera->activo)
<form action="{{ route('carreras.destroy', $carrera) }}" method="POST" class="d-inline"
      onsubmit="return confirm('¿Desactivar esta carrera?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm btn-danger">Desactivar</button>
</form>
@else
<form action="{{ route('carreras.reactivar', $carrera) }}" method="POST" class="d-inline"
      onsubmit="return confirm('¿Reactivar esta carrera?')">
    @csrf
    <button class="btn btn-sm btn-success">Reactivar</button>
</form>
@endif
                </td>
                <td>
                    <a href="{{ route('carreras.edit', $carrera) }}" class="btn btn-sm btn-warning">Editar</a>
                    @if($carrera->activo)
                    <form action="{{ route('carreras.destroy', $carrera) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('¿Desactivar esta carrera?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Desactivar</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted">No hay carreras registradas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
</body>
</html>