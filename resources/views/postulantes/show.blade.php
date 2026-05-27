<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle Postulante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4" style="max-width:700px">
    <h2>🎓 Detalle del Postulante</h2>
    <a href="{{ route('postulantes.index') }}" class="btn btn-secondary mb-3">← Volver</a>
    <a href="{{ route('postulantes.edit', $postulante) }}" class="btn btn-warning mb-3">✏️ Editar</a>

    <div class="card p-4 bg-white mb-3">
        <h5 class="text-primary">📋 Datos Personales</h5>
        <table class="table table-borderless mb-0">
            <tr><th width="40%">CI</th><td>{{ $postulante->persona->ci }}</td></tr>
            <tr><th>Nombre</th><td>{{ $postulante->persona->nombre }}</td></tr>
            <tr><th>Fecha nacimiento</th><td>{{ optional($postulante->persona->fecha_nacimiento)->format('d/m/Y') ?? '—' }}</td></tr>
            <tr><th>Sexo</th><td>{{ $postulante->persona->sexo == 'M' ? 'Masculino' : ($postulante->persona->sexo == 'F' ? 'Femenino' : '—') }}</td></tr>
            <tr><th>Dirección</th><td>{{ $postulante->persona->direccion ?? '—' }}</td></tr>
            <tr><th>Teléfono</th><td>{{ $postulante->persona->telefono ?? '—' }}</td></tr>
            <tr><th>Correo</th><td>{{ $postulante->persona->correo ?? '—' }}</td></tr>
            <tr><th>Colegio</th><td>{{ $postulante->colegio }}</td></tr>
            <tr>
                <th>Estado</th>
                <td>
                    @php $badges = ['pendiente'=>'warning','inscrito'=>'primary','aprobado'=>'success','reprobado'=>'danger']; @endphp
                    <span class="badge bg-{{ $badges[$postulante->estado] ?? 'secondary' }}">
                        {{ ucfirst($postulante->estado) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    @foreach($postulante->inscripciones as $inscripcion)
    <div class="card p-4 bg-white">
        <h5 class="text-primary">📚 Inscripción — Periodo {{ $inscripcion->periodo->fecha_ini_inscripcion->format('Y') }}</h5>
        <p><strong>Fecha:</strong> {{ $inscripcion->fecha_inscripcion->format('d/m/Y') }}</p>
        <p><strong>Estado:</strong> {{ ucfirst($inscripcion->estado) }}</p>

        <h6>Carreras postuladas:</h6>
        <ul>
            @foreach($inscripcion->postulacionCarreras as $pc)
                <li>
                    <strong>Opción {{ $pc->prioridad }}:</strong>
                    {{ $pc->carrera->codigo }} — {{ $pc->carrera->nombre }}
                </li>
            @endforeach
        </ul>
    </div>
    @endforeach
</div>
</body>
</html>