@extends('layouts.base')
@section('titulo', 'Mi Panel — CUP')
@section('contenido')

<div class="page-header mb-4">
    <h1><i class="bi bi-person-circle me-2"></i>Mi Panel</h1>
    <p class="page-subtitle">
        Bienvenido/a, <strong>{{ Auth::user()->name }}</strong>
    </p>
</div>

{{-- ══ SIN INSCRIPCIÓN ══ --}}
@if(!$inscripcion)
    <div class="alert alert-warning d-flex align-items-start gap-2">
        <i class="bi bi-exclamation-triangle mt-1"></i>
        <div>
            <strong>No se encontró inscripción asociada a tu cuenta.</strong><br>
            Contacta a la administración de la FICCT.
            <br><small class="text-muted">Email: admision.ficct@uagrm.edu.bo | Tel: (591-3) 352-0000</small>
        </div>
    </div>

{{-- ══ PAGO RECHAZADO ══ --}}
@elseif($estadoInscripcion === 'pago_rechazado')
    <div class="alert alert-danger d-flex align-items-start gap-2">
        <i class="bi bi-x-circle-fill fs-4 mt-1"></i>
        <div>
            <strong>Tu pago fue rechazado.</strong><br>
            No puedes acceder a la información académica hasta regularizar tu pago.
            @if($inscripcion->pago?->observacion)
                <br><span class="text-muted">Motivo: {{ $inscripcion->pago->observacion }}</span>
            @endif
        </div>
    </div>
    <div class="panel-cup mt-4">
        <div class="panel-cup-header" style="background:#dc3545;color:#fff;">
            <strong><i class="bi bi-telephone me-1"></i> ¿Qué puedo hacer?</strong>
        </div>
        <div class="panel-cup-body">
            <p class="mb-2">Para regularizar tu situación contáctate con la administración:</p>
            <ul class="mb-3">
                <li><i class="bi bi-envelope me-1"></i> <strong>Email:</strong> admision.ficct@uagrm.edu.bo</li>
                <li><i class="bi bi-telephone me-1"></i> <strong>Teléfono:</strong> (591-3) 352-0000</li>
                <li><i class="bi bi-clock me-1"></i> <strong>Horario:</strong> Lunes a Viernes 8:00 - 18:00</li>
            </ul>
            <a href="{{ route('pagos.seleccionar', $inscripcion) }}" class="btn btn-danger">
                <i class="bi bi-credit-card me-1"></i> Reintentar pago
            </a>
        </div>
    </div>

{{-- ══ PAGO PENDIENTE ══ --}}
@elseif($estadoInscripcion === 'pago_pendiente')
    <div class="alert alert-warning d-flex align-items-start gap-2">
        <i class="bi bi-hourglass-split fs-4 mt-1"></i>
        <div>
            <strong>Tu pago está siendo verificado.</strong><br>
            El administrador revisará tu pago en breve. Recibirás un correo cuando sea confirmado.
            @if($inscripcion->pago?->referencia_qr)
                <br><small>Referencia: <code>{{ $inscripcion->pago->referencia_qr }}</code></small>
            @endif
        </div>
    </div>

{{-- ══ SIN PAGO / ACTIVA ══ --}}
@elseif(!$estadoPago || $estadoInscripcion === 'activa')
    <div class="alert alert-info d-flex align-items-start gap-2">
        <i class="bi bi-credit-card fs-4 mt-1"></i>
        <div>
            <strong>Debes completar tu pago para acceder al sistema.</strong><br>
            Selecciona tu método de pago preferido para continuar.
        </div>
    </div>
    <a href="{{ route('pagos.seleccionar', $inscripcion) }}" class="btn btn-primary">
        <i class="bi bi-credit-card me-1"></i> Realizar pago ahora
    </a>

{{-- ══ PAGO APROBADO / HABILITADO ══ --}}
@else
    <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <strong>¡Pago confirmado! Estás habilitado para el Curso Preuniversitario.</strong>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="kpi-card kpi-primary h-100">
                <div class="kpi-icon"><i class="bi bi-people"></i></div>
                <div class="kpi-value" style="font-size:1.25rem;">Pendiente de asignación</div>
                <div class="kpi-label">Mi Grupo</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-warning h-100">
                <div class="kpi-icon"><i class="bi bi-clock"></i></div>
                <div class="kpi-value" style="font-size:1.25rem;">Pendiente</div>
                <div class="kpi-label">Mi Horario</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card kpi-accent h-100">
                <div class="kpi-icon"><i class="bi bi-graph-up"></i></div>
                <div class="kpi-value" style="font-size:1.25rem;">No disponibles aún</div>
                <div class="kpi-label">Mis Notas</div>
            </div>
        </div>
    </div>

    <div class="panel-cup mb-4">
        <div class="panel-cup-header">
            <strong><i class="bi bi-clipboard-check me-1"></i> Mi Inscripción</strong>
        </div>
        <div class="panel-cup-body">
            <table class="table table-sm mb-0">
                <tr>
                    <td class="text-muted" width="200">Nombre completo:</td>
                    <td><strong>{{ $inscripcion->postulante->persona->nombre ?? '—' }}</strong></td>
                </tr>
                <tr>
                    <td class="text-muted">CI:</td>
                    <td>{{ $inscripcion->postulante->persona->ci ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="text-muted">Periodo:</td>
                    <td>
                        {{ $inscripcion->periodo->fecha_ini_inscripcion->format('d/m/Y') }}
                        —
                        {{ $inscripcion->periodo->fecha_fin_curso->format('d/m/Y') }}
                    </td>
                </tr>
                <tr>
                    <td class="text-muted">Carrera(s) postulada(s):</td>
                    <td>
                        @foreach($inscripcion->postulacionCarreras as $pc)
                            <span class="badge bg-secondary">
                                {{ $pc->prioridad }}. {{ $pc->carrera->nombre }}
                            </span>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <td class="text-muted">Estado:</td>
                    <td><span class="badge bg-success">Habilitado ✅</span></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="panel-cup">
        <div class="panel-cup-header">
            <strong><i class="bi bi-info-circle me-1"></i> Próximos pasos</strong>
        </div>
        <div class="panel-cup-body">
            <p class="text-muted mb-0" style="font-size:0.92rem;line-height:1.6;">
                Tu inscripción está confirmada. Pronto la administración asignará
                los grupos, horarios y docentes. Recibirás una notificación cuando
                tu grupo esté disponible.
            </p>
        </div>
    </div>
@endif

@endsection