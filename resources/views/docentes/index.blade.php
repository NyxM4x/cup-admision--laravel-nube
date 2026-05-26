<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Docentes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>👨‍🏫 Gestión de Docentes</h2>
        <a href="{{ route('docentes.create') }}" class="btn btn-primary">+ Nuevo Docente</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>CI</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Profesión</th>
                        <th>Exp.</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($docentes as $docente)
                    <tr class="{{ !$docente->activo ? 'table-secondary' : '' }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $docente->persona->ci }}</td>
                        <td>{{ $docente->persona->nombre }}</td>
                        <td>{{ $docente->persona->telefono ?? '—' }}</td>
                        <td>{{ $docente->persona->correo ?? '—' }}</td>
                        <td>{{ $docente->profesion->nombre ?? '—' }}</td>
                        <td>{{ $docente->anios_experiencia }} años</td>
                        <td>
                            @if($docente->activo)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('docentes.edit', $docente) }}" 
                               class="btn btn-sm btn-warning">Editar</a>

                            @if($docente->activo)
                                <form action="{{ route('docentes.destroy', $docente) }}" 
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Desactivar este docente?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Desactivar</button>
                                </form>
                            @else
                                <form action="{{ route('docentes.reactivar', $docente) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Reactivar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            No hay docentes registrados aún.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>