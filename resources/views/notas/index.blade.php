@extends('layouts.base')
@section('titulo', 'Gestión de Notas — CUP')
@section('contenido')

<div class="page-header mb-4">
    <h1><i class="bi bi-journal-check me-2"></i>Gestión de Notas</h1>
    <p class="page-subtitle">CU21 — Selecciona un grupo para registrar las notas de sus postulantes</p>
</div>

{{-- Filtro por periodo --}}
<form method="GET" class="row g-2 align-items-center mb-4">
    <div class="col-md-4">
        <select name="periodo_id" class="form-select" onchange="this.form.submit()">
            <option value="todos">Todos los periodos</option>
            @foreach($periodos as $per)
                <option value="{{ $per->id }}" {{ (int)$periodoId === $per->id ? 'selected' : '' }}>
                    Periodo #{{ $per->id }} {{ $per->activo ? '(activo)' : '' }}
                </option>
            @endforeach
        </select>
    </div>
</form>

@if($grupos->isEmpty())
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>
        No hay grupos activos para este periodo. Crea y configura grupos primero.
    </div>
@else
    <div class="row g-3">
        @foreach($grupos->groupBy(fn($g) => $g->horario?->turno ?? 'Sin turno') as $turno => $gruposTurno)
            <div class="col-12">
                <h5 class="text-muted mb-2">
                    <i class="bi bi-{{ $turno === 'Mañana' ? 'sun' : 'sunset' }} me-1"></i>
                    Turno {{ $turno }}
                </h5>
            </div>
            @foreach($gruposTurno as $grupo)
                <div class="col-md-4">
                    <div class="panel-cup h-100">
                        <div class="panel-cup-header d-flex justify-content-between align-items-center"
                             style="background:{{ $turno === 'Mañana' ? '#ffc107' : '#fd7e14' }};color:#000;">
                            <strong>{{ $grupo->codigo }}</strong>
                            <span class="badge bg-dark">
                                {{ $grupo->inscritos_actuales }}/{{ $grupo->cupo_max }}
                            </span>
                        </div>
                        <div class="panel-cup-body">
                            <ul class="list-unstyled mb-3 small">
                                @foreach($grupo->grupoMaterias as $gm)
                                    <li class="mb-1">
                                        <span class="badge bg-secondary">{{ $gm->materia?->sigla }}</span>
                                        {{ $gm->materia?->nombre }}
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('notas.show', $grupo) }}"
                               class="btn btn-cup-primary btn-sm w-100">
                                <i class="bi bi-pencil-square me-1"></i> Gestionar notas
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
@endif

@endsection