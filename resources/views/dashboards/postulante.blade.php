@extends('layouts.base')
@section('titulo', 'Mi Panel — CUP')
@section('contenido')

<div class="page-header mb-4">
    <h1><i class="bi bi-person-circle me-2"></i>Mi Panel</h1>
    <p class="page-subtitle">
        Bienvenido/a, <strong>{{ Auth::user()->name }}</strong>
    </p>
</div>

{{-- ══════════════════════════════════════════════════════ --}}
{{-- BLOQUE PRINCIPAL: VALIDACIÓN DE INSCRIPCIÓN Y PAGO --}}
{{-- ══════════════════════════════════════════════════════ --}}

@if(!$inscripcion)
    {{-- Caso 1: Sin inscripción --}}
    <div class="alert alert-warning d-flex align-items-start gap-2">
        <i class="bi bi-exclamation-triangle mt-1"></i>
        <div>
            <strong>No se encontró inscripción asociada a tu cuenta.</strong><br>
            Contacta a la administración de la FICCT.
            <br><small class="text-muted">Email: admision.ficct@uagrm.edu.bo | Tel: (591-3) 352-0000</small>
        </div>
    </div>

@elseif($estadoInscripcion === 'pago_rechazado')
    {{-- Caso 2: Pago rechazado --}}
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

@elseif($estadoInscripcion === 'pago_pendiente')
    {{-- Caso 3: Pago pendiente --}}
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

@elseif(!$estadoPago || $estadoInscripcion === 'activa')
    {{-- Caso 4: Sin pago realizado --}}
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

@else
    {{-- Caso 5: PAGO APROBADO — CONTENIDO PRINCIPAL --}}
    <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <strong>¡Pago confirmado! Estás habilitado para el Curso Preuniversitario.</strong>
    </div>

    {{-- Panel: Mi Inscripción --}}
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

    {{-- Panel: Mi Turno --}}
    @if($grupo)
        @php
            $turno = $grupo->horario->turno ?? '';
            $esMañana = $turno === 'Mañana';
        @endphp
        <div class="panel-cup mb-4">
            <div class="panel-cup-header"
                 style="background: {{ $esMañana ? '#ffc107' : '#fd7e14' }}; color:#000;">
                <strong>
                    <i class="bi bi-{{ $esMañana ? 'sun' : 'sunset' }} me-1"></i>
                    Mi Turno: {{ $turno ?: '—' }} — {{ $grupo->codigo }}
                </strong>
            </div>
            <div class="panel-cup-body p-0">
                <table class="table table-bordered mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="40">#</th>
                            <th>Materia</th>
                            <th>Horario</th>
                            <th>Aula</th>
                            <th>Docente</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grupo->grupoMaterias->sortBy('orden') as $gm)
                            <tr>
                                <td class="text-center text-muted">{{ $gm->orden }}</td>
                                <td>
                                    <strong>{{ $gm->materia->nombre ?? '—' }}</strong>
                                    <small class="text-muted ms-1">({{ $gm->materia->sigla ?? '' }})</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $gm->rango }}</span>
                                </td>
                                <td>
                                    @php $aulaEf = $gm->aula ?? $grupo->aula; @endphp
                                    {{ $aulaEf ? ($aulaEf->edificio ? $aulaEf->edificio.' '.$aulaEf->codigo : $aulaEf->codigo) : '—' }}
                                </td>
                                <td>{{ $gm->docente->persona->nombre ?? 'Por asignar' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    Sin materias configuradas aún.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        {{-- Sin turno elegido aún --}}
        <div class="panel-cup mb-4">
            <div class="panel-cup-header">
                <strong><i class="bi bi-calendar3 me-1"></i> Mi Horario</strong>
            </div>
            <div class="panel-cup-body text-center py-4">
                <i class="bi bi-hourglass-split fs-1 text-muted"></i>
                <p class="text-muted mt-2 mb-3">Aún no has elegido tu turno.</p>
                <a href="{{ route('grupos.seleccionar', $inscripcion) }}" class="btn btn-primary">
                    <i class="bi bi-calendar-check me-1"></i> Elegir mi turno
                </a>
            </div>
        </div>
    @endif

    {{-- Panel: Mis Notas (CU21/CU22) --}}
    @if($grupo)
        <div class="panel-cup">
            <div class="panel-cup-header">
                <strong><i class="bi bi-graph-up me-1"></i> Mis Calificaciones</strong>
            </div>
            <div class="panel-cup-body p-0">
                @php
                    $tieneNotas = $grupo->grupoMaterias()
                        ->whereHas('notas', fn($q) => $q->where('postulante_id', $inscripcion->postulante->id))
                        ->exists();
                @endphp

                @if($tieneNotas)
                    <table class="table table-sm table-bordered mb-0 align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th width="30%">Materia</th>
                                <th width="15%">Examen 1</th>
                                <th width="15%">Examen 2</th>
                                <th width="15%">Examen 3</th>
                                <th width="15%">Nota Final</th>
                                <th width="10%">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($grupo->grupoMaterias->sortBy('orden') as $gm)
                                @php
                                    $nota = $gm->notas()
                                        ->where('postulante_id', $inscripcion->postulante->id)
                                        ->first();
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $gm->materia->nombre }}</strong>
                                        <small class="text-muted ms-1">({{ $gm->materia->sigla }})</small>
                                    </td>
                                    <td class="text-center">
                                        {{ $nota?->examen1 ? number_format($nota->examen1, 2) : '—' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $nota?->examen2 ? number_format($nota->examen2, 2) : '—' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $nota?->examen3 ? number_format($nota->examen3, 2) : '—' }}
                                    </td>
                                    <td class="text-center">
                                        <strong>{{ $nota?->nota_formateada ?? '—' }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @if($nota)
                                            @if($nota->resultado === 'aprobado')
                                                <span class="badge bg-success">Aprobado</span>
                                            @elseif($nota->resultado === 'reprobado')
                                                <span class="badge bg-danger">Reprobado</span>
                                            @else
                                                <span class="badge bg-secondary">Pendiente</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Sin datos</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">
                                        No hay materias configuradas aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-hourglass-split fs-1"></i>
                        <p class="mt-2 mb-0">
                            Las notas estarán disponibles después de los exámenes.<br>
                            <small>El docente registrará tus calificaciones en el sistema.</small>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @else
        {{-- Sin turno elegido aún --}}
        <div class="panel-cup">
            <div class="panel-cup-header">
                <strong><i class="bi bi-graph-up me-1"></i> Mis Calificaciones</strong>
            </div>
            <div class="panel-cup-body text-center py-4 text-muted">
                <i class="bi bi-hourglass-split fs-1"></i>
                <p class="mt-2 mb-0">
                    Elige tu turno primero para ver tus calificaciones.
                </p>
            </div>
        </div>
    @endif

@endif {{-- Fin bloque principal --}}

@endsection
