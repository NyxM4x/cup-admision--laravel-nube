<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Materias CUP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Materias del CUP</h2>
        <a href="{{ route('materias.create') }}" class="btn btn-primary">+ Nueva Materia</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped bg-white">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Sigla</th>
                <th>Nombre</th>
                <th>Días</th>
                <th>Exámenes</th>
                <th>Peso E1</th>
                <th>Peso E2</th>
                <th>Peso E3</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($materias as $materia)
            <tr>
                <td>{{ $materia->id }}</td>
                <td><span class="badge bg-dark">{{ $materia->sigla }}</span></td>
                <td>{{ $materia->nombre }}</td>
                <td><span class="badge bg-info text-dark">{{ $materia->dias }}</span></td>
                <td class="text-center">{{ $materia->cant_examenes }}</td>
                <td class="text-center">{{ $materia->peso_examen1 }}%</td>
                <td class="text-center">{{ $materia->peso_examen2 }}%</td>
                <td class="text-center">{{ $materia->peso_examen3 }}%</td>
                <td>
                    @if($materia->activo)
                        <span class="badge bg-success">Activa</span>
                    @else
                        <span class="badge bg-secondary">Inactiva</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('materias.edit', $materia) }}" class="btn btn-sm btn-warning">Editar</a>
                    @if($materia->activo)
                        <form action="{{ route('materias.destroy', $materia) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Desactivar esta materia?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Desactivar</button>
                        </form>
                    @else
                        <form action="{{ route('materias.reactivar', $materia) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Reactivar esta materia?')">
                            @csrf
                            <button class="btn btn-sm btn-success">Reactivar</button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center text-muted">No hay materias registradas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
</body>
</html>