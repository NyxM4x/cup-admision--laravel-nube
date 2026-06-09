<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estado de Pago — CUP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4" style="max-width:600px">
    <h2>Estado de tu Pago</h2>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary mb-3">← Volver</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body text-center py-5">

            @if($inscripcion->estado === 'pago_pendiente')
                <div class="display-1 mb-3">⏳</div>
                <h4 class="text-warning">Pago en revisión</h4>
                <p class="text-muted">El administrador verificará tu pago en breve.</p>
                <div class="alert alert-info mt-3">
                    <strong>Referencia:</strong> <code>{{ $pago->referencia_qr ?? 'N/A' }}</code><br>
                    Puedes cerrar esta ventana. Recibirás un correo cuando sea confirmado.
                </div>

            @elseif(in_array($inscripcion->estado, ['pago_aprobado', 'habilitado']))
                <div class="display-1 mb-3">✅</div>
                <h4 class="text-success">¡Pago Aprobado!</h4>
                <p class="text-muted">Estás habilitado para el curso preuniversitario.</p>
                <div class="alert alert-success mt-3">
                    Revisa tu correo electrónico — te enviamos tus credenciales de acceso al sistema.
                </div>

            @elseif($inscripcion->estado === 'pago_rechazado')
                <div class="display-1 mb-3">❌</div>
                <h4 class="text-danger">Pago Rechazado</h4>
                @if($pago && $pago->observacion)
                    <div class="alert alert-danger mt-3">
                        <strong>Motivo:</strong> {{ $pago->observacion }}
                    </div>
                @endif
                <a href="{{ route('pagos.qr', $inscripcion) }}" class="btn btn-primary mt-3">
                    🔄 Intentar nuevamente
                </a>

            @else
                <div class="display-1 mb-3">📋</div>
                <h4>Sin pago registrado</h4>
                <a href="{{ route('pagos.qr', $inscripcion) }}" class="btn btn-primary mt-3">
                    💳 Realizar pago ahora
                </a>
            @endif

        </div>
    </div>
</div>
</body>
</html>