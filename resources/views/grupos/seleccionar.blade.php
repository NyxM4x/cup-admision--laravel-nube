@extends('layouts.base')
@section('titulo', 'Seleccionar Grupo — CUP')
@section('contenido')

<div class="page-header mb-4">
    <h1><i class="bi bi-people me-2"></i>Seleccionar Grupo</h1>
    <p class="page-subtitle">Elige tu grupo para cada materia del Curso Preuniversitario</p>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

{{-- Grupos ya asignados --}}
@if($gruposAsignados->count() > 0)
    <div class="panel-cup mb-4">
        <div class="panel-cup-header" style="background:#198754;color:#fff;">
            <strong><i class="bi bi-check-circle me-1"></i> Mis grupos confirmados</strong>
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
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($gruposAsignados as $ga)
                    <tr>
                        <td><strong>{{ $ga->materia->nombre }}</strong></td>
                        <td><span class="badge bg-success">{{ $ga->codigo }}</span></td>
                        <td><span class="badge bg-info text-dark">{{ $ga->horario->turno ?? '—' }}</span></td>
                        <td>{{ $ga->horario->dias ?? '—' }}</td>
                        <td>
                            @if($ga->horario)
                                {{ substr($ga->horario->hora_inicio, 0, 5) }} — {{ substr($ga->horario->hora_fin, 0, 5) }}
                            @else —
                            @endif
                        </td>
                        <td>{{ $ga->aula ? $ga->aula->edificio . ' ' . $ga->aula->codigo : '—' }}</td>
                        <td>{{ $ga->docente->persona->nombre ?? 'Por asignar' }}</td>
                        <td>
                            <form action="{{ route('grupos.abandonar', [$inscripcion, $ga]) }}" method="POST"
                                  onsubmit="return confirm('¿Salir de este grupo?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Salir</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Grupos disponibles por turno --}}
@if($gruposPorTurno->isEmpty())
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-1"></i>
        Aún no hay grupos disponibles para este periodo. La administración los habilitará próximamente.
    </div>
@else
    @foreach(['Mañana', 'Tarde', 'Noche'] as $turno)
        @if($gruposPorTurno->has($turno))
        <div class="panel-cup mb-4">
            <div class="panel-cup-header d-flex align-items-center gap-2"
                 style="background: {{ $turno === 'Mañana' ? '#ffc107' : ($turno === 'Tarde' ? '#fd7e14' : '#343a40') }};
                        color: {{ $turno === 'Noche' ? '#fff' : '#000' }}">
                <i class="bi bi-{{ $turno === 'Mañana' ? 'sun' : ($turno === 'Tarde' ? 'sunset' : 'moon') }} me-1"></i>
                <strong>Turno {{ $turno }}</strong>
            </div>
            <div class="panel-cup-body p-0">
                <table class="table table-bordered mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Materia</th>
                            <th>Código</th>
                            <th>Días</th>
                            <th>Horario</th>
                            <th>Aula</th>
                            <th>Docente</th>
                            <th>Cupos</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gruposPorTurno[$turno] as $grupo)
                        @php
                            $yaInscrito = $gruposAsignados->contains('id', $grupo->id);
                            $yaEnMateria = $gruposAsignados->contains('materia_id', $grupo->materia_id);
                            $sinCupo = $grupo->inscritos_actuales >= $grupo->cupo_max;
                        @endphp
                        <tr class="{{ $yaInscrito ? 'table-success' : ($sinCupo ? 'table-secondary' : '') }}">
                            <td><strong>{{ $grupo->materia->nombre }}</strong>
                                <small class="text-muted">({{ $grupo->materia->sigla }})</small>
                            </td>
                            <td><span class="badge bg-primary">{{ $grupo->codigo }}</span></td>
                            <td>{{ $grupo->horario->dias ?? '—' }}</td>
                            <td>
                                {{ substr($grupo->horario->hora_inicio ?? '', 0, 5) }}
                                — {{ substr($grupo->horario->hora_fin ?? '', 0, 5) }}
                            </td>
                            <td>{{ $grupo->aula ? $grupo->aula->edificio . ' ' . $grupo->aula->codigo : '—' }}</td>
                            <td>{{ $grupo->docente->persona->nombre ?? 'Por asignar' }}</td>
                            <td>
                                <span class="badge {{ $sinCupo ? 'bg-danger' : 'bg-success' }}">
                                    {{ $grupo->inscritos_actuales }}/{{ $grupo->cupo_max }}
                                </span>
                            </td>
                            <td>
                                @if($yaInscrito)
                                    <span class="badge bg-success">✅ Inscrito</span>
                                @elseif($yaEnMateria)
                                    <span class="badge bg-secondary">Ya tienes grupo</span>
                                @elseif($sinCupo)
                                    <span class="badge bg-danger">Sin cupo</span>
                                @else
                                    <form action="{{ route('grupos.confirmar', $inscripcion) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="grupo_id" value="{{ $grupo->id }}">
                                        <button type="submit" class="btn btn-sm btn-primary"
                                                onclick="return confirm('¿Confirmar inscripción en {{ $grupo->codigo }}?')">
                                            Elegir
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @endforeach
@endif

<div class="mt-3">
    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver al Dashboard
    </a>
</div>

@endsection