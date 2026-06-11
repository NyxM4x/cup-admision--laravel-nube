<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago QR — CUP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
</head>
<body class="bg-light">
<div class="container mt-4" style="max-width:600px">
    <h2 class="mb-1">💳 Pago de Inscripción</h2>
    <p class="text-muted">Curso Preuniversitario — FICCT UAGRM</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body text-center">
            <h5 class="mb-3">Escanea el QR para realizar el pago</h5>

            <div id="qrcode" class="d-flex justify-content-center mb-3"></div>

            <div class="alert alert-info py-2">
                <strong>Referencia:</strong> <code>{{ $pago->referencia_qr }}</code>
            </div>

            <table class="table table-sm text-start mt-3">
                <tr>
                    <td><strong>Postulante:</strong></td>
                    <td>{{ $inscripcion->postulante->persona->nombre ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Monto:</strong></td>
                    <td class="text-success fw-bold">Bs. {{ number_format($pago->monto, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Método:</strong></td>
                    <td>QR / Tigo Money / Simple</td>
                </tr>
            </table>

            <div class="alert alert-warning text-start mt-3">
                <strong>Instrucciones:</strong>
                <ol class="mb-0 mt-1">
                    <li>Abre tu app bancaria (Tigo Money, Simple, etc.)</li>
                    <li>Escanea el QR o transfiere usando la referencia</li>
                    <li>Presiona el botón de abajo cuando hayas pagado</li>
                </ol>
            </div>

            <form action="{{ route('pagos.confirmar', $inscripcion) }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-success btn-lg w-100">
                    ✅ Ya realicé el pago — Notificar al administrador
                </button>
            </form>

            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary mt-2 w-100">
                Volver al inicio
            </a>
        </div>
    </div>
</div>
<script>
    new QRCode(document.getElementById("qrcode"), {
        text: "{{ $pago->referencia_qr }}",
        width: 200,
        height: 200,
        colorDark: "#000000",
        colorLight: "#ffffff",
    });
</script>
</body>
</html>