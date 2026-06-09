@extends('layouts.base')

@section('titulo', 'Panel de Pagos')

@section('contenido')

<div class="page-header mb-4">
    <h1><i class="bi bi-credit-card-fill me-2"></i>Panel de Pagos</h1>
    <p class="page-subtitle">Aprueba o rechaza los pagos QR enviados por los postulantes</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- PENDIENTES --}}
<div class="panel-cup mb-4">
    <div class="panel-cup-header d-flex justify-content-between align-items-center"
         style="background:#ffc107;color:#000;">
        <strong><i class="bi bi-hourglass-split me-1"></i> Pendientes de Revisión</strong>
        <span class="badge bg-dark fs-6">{{ $pagos->count() }}</span>
    </div>
    <div class="panel-cup-body p-0">
        <table class="table table-bordered mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Postulante</th>
                    <th>CI</th>
                    <th>Correo</th>
                    <th>Carrera(s)</th>
                    <th>Referencia QR</th>
                    <th>Monto</th>
                    <th>Enviado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pagos as $pago)
                <tr>
                    <td><strong>{{ $pago->inscripcion->postulante->persona->nombre ?? 'N/A' }}</strong></td>
                    <td>{{ $pago->inscripcion->postulante->persona->ci ?? '—' }}</td>
                    <td>{{ $pago->inscripcion->postulante->persona->correo ?? '—' }}</td>
                    <td>
                        @foreach($pago->inscripcion->postulacionCarreras as $pc)
                            <span class="badge bg-secondary">{{ $pc->prioridad }}. {{ $pc->carrera->nombre }}</span>
                        @endforeach
                    </td>
                    <td><code>{{ $pago->referencia_qr }}</code></td>
                    <td class="text-success fw-bold">Bs. {{ number_format($pago->monto, 2) }}</td>
                    <td>{{ $pago->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        {{-- Aprobar --}}
                        <form action="{{ route('pagos.aprobar', $pago) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Confirmar aprobación del pago?')">
                            @csrf
                            <button class="btn btn-sm btn-success">
                                <i class="bi bi-check-circle me-1"></i>Aprobar
                            </button>
                        </form>

                        {{-- Rechazar --}}
                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                data-bs-target="#modalRechazar{{ $pago->id }}">
                            <i class="bi bi-x-circle me-1"></i>Rechazar
                        </button>

                        {{-- Modal rechazo --}}
                        <div class="modal fade" id="modalRechazar{{ $pago->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Rechazar Pago</h5>
                                        <button type="button" class="btn-close btn-close-white"
                                                data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('pagos.rechazar', $pago) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <p><strong>Postulante:</strong>
                                                {{ $pago->inscripcion->postulante->persona->nombre }}</p>
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Motivo del rechazo <span class="text-danger">*</span>
                                                </label>
                                                <textarea name="observacion" class="form-control" rows="3"
                                                    placeholder="ej: No se encontró depósito con esa referencia"
                                                    required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-danger">
                                                Confirmar Rechazo
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        🎉 No hay pagos pendientes de revisión.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- RESUELTOS --}}
<div class="panel-cup">
    <div class="panel-cup-header" style="background:#6c757d;color:#fff;">
        <strong><i class="bi bi-clock-history me-1"></i> Últimos Pagos Resueltos</strong>
    </div>
    <div class="panel-cup-body p-0">
        <table class="table table-bordered mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Postulante</th>
                    <th>Correo</th>
                    <th>Referencia</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Observación</th>
                    <th>Revisado por</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pagosResueltos as $pago)
                <tr>
                    <td>{{ $pago->inscripcion->postulante->persona->nombre ?? 'N/A' }}</td>
                    <td>{{ $pago->inscripcion->postulante->persona->correo ?? '—' }}</td>
                    <td><code>{{ $pago->referencia_qr }}</code></td>
                    <td>Bs. {{ number_format($pago->monto, 2) }}</td>
                    <td>
                        @if($pago->estado === 'aprobado')
                            <span class="badge bg-success">✅ Aprobado</span>
                        @else
                            <span class="badge bg-danger">❌ Rechazado</span>
                        @endif
                    </td>
                    <td>{{ $pago->observacion ?? '—' }}</td>
                    <td>{{ $pago->revisor->name ?? '—' }}</td>
                    <td>{{ $pago->updated_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">Sin pagos resueltos aún.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection