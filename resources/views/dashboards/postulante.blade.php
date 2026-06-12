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

    {{-- Datos de inscripción --}}
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

{{-- Grupos asignados --}}
@if($grupos->count() > 0)
    <div class="panel-cup mb-4">
        <div class="panel-cup-header">
            <strong><i class="bi bi-people me-1"></i> Mis Grupos y Horarios</strong>
        </div>
        <div class="panel-cup-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Materia</th>
                        <th>Grupo</th>
                        <th>Turno</th>
                        <th>Días</th>
                        <th>Horario</th>
                        <th>Aula</th>
                        <th>Docente</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grupos as $grupo)
                    <tr>
                        <td><strong>{{ $grupo->materia->nombre ?? '—' }}</strong></td>
                        <td><span class="badge bg-primary">{{ $grupo->codigo }}</span></td>
                        <td>
                            @if($grupo->horario)
                                <span class="badge bg-info text-dark">{{ $grupo->horario->turno }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $grupo->horario->dias ?? '—' }}</td>
                        <td>
                            @if($grupo->horario)
                                {{ \Carbon\Carbon::parse($grupo->horario->hora_inicio)->format('H:i') }}
                                —
                                {{ \Carbon\Carbon::parse($grupo->horario->hora_fin)->format('H:i') }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $grupo->aula ? $grupo->aula->edificio . ' - ' . $grupo->aula->codigo : '—' }}</td>
                        <td>{{ $grupo->docente->persona->nombre ?? 'Por asignar' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{-- 👇 AGREGAR BOTÓN MODIFICAR DESPUÉS DE LA TABLA --}}
            <div class="p-3 text-end border-top">
                <a href="{{ route('grupos.seleccionar', $inscripcion) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i> Modificar mis grupos
                </a>
            </div>
        </div>
    </div>
@else
    <div class="panel-cup mb-4">
        <div class="panel-cup-header">
            <strong><i class="bi bi-people me-1"></i> Mis Grupos y Horarios</strong>
        </div>
        <div class="panel-cup-body text-center py-4">
            <i class="bi bi-hourglass-split fs-1 text-muted"></i>
            <p class="text-muted mt-2 mb-0">
                Tu grupo aún no ha sido asignado.<br>
                <small>La administración asignará los grupos próximamente.</small>
            </p>
            {{-- 👇 AGREGAR BOTÓN DE SELECCIONAR AQUÍ --}}
            <div class="mt-3">
                <a href="{{ route('grupos.seleccionar', $inscripcion) }}" class="btn btn-primary">
                    <i class="bi bi-people me-1"></i> Seleccionar mis grupos y horarios
                </a>
            </div>
        </div>
    </div>
@endif

    {{-- Notas --}}
    <div class="panel-cup">
        <div class="panel-cup-header">
            <strong><i class="bi bi-graph-up me-1"></i> Mis Notas</strong>
        </div>
        <div class="panel-cup-body text-center py-4">
            <i class="bi bi-journal-text fs-1 text-muted"></i>
            <p class="text-muted mt-2 mb-0">
                Las notas estarán disponibles después de los exámenes.<br>
                <small>El docente registrará tus calificaciones en el sistema.</small>
            </p>
        </div>
    </div>
@endif

@endsection