@extends('layouts.base')
@section('titulo', 'Mi Horario — CUP')
@section('contenido')

<div class="page-header mb-4">
    <h1><i class="bi bi-calendar3 me-2"></i>Mi Horario</h1>
    <p class="page-subtitle">Curso Preuniversitario — FICCT UAGRM</p>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if($grupoAsignado)
    {{-- VISTA SOLO LECTURA --}}
    <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <strong>Ya tienes un turno asignado: {{ $grupoAsignado->horario->turno ?? '—' }} ({{ $grupoAsignado->codigo }})</strong>
    </div>

    <div class="panel-cup mb-4">
        <div class="panel-cup-header" style="background: {{ ($grupoAsignado->horario->turno ?? '') === 'Mañana' ? '#ffc107' : '#fd7e14' }}; color:#000;">
            <strong><i class="bi bi-{{ ($grupoAsignado->horario->turno ?? '') === 'Mañana' ? 'sun' : 'sunset' }} me-1"></i>
                Turno {{ $grupoAsignado->horario->turno ?? '—' }} — {{ $grupoAsignado->horario->dias ?? '' }}
            </strong>
        </div>
        <div class="panel-cup-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th><th>Materia</th><th>Horario</th><th>Aula</th><th>Docente</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grupoAsignado->grupoMaterias->sortBy('orden') as $gm)
                    <tr>
                        <td>{{ $gm->orden }}</td>
                        <td><strong>{{ $gm->materia->nombre ?? '—' }}</strong></td>
                        <td>{{ $gm->rango }}</td>
                        <td>
                            @php $aulaEf = $gm->aula ?? $grupoAsignado->aula; @endphp
                            {{ $aulaEf ? $aulaEf->edificio . ' ' . $aulaEf->codigo : '—' }}
                        </td>
                        <td>{{ $gm->docente->persona->nombre ?? 'Por asignar' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver a mi panel
    </a>

@else
    {{-- SELECCIÓN DE TURNO --}}
    @if($gruposDisponibles->isEmpty())
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-1"></i>
            Aún no hay turnos disponibles. La administración los habilitará próximamente.
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver a mi panel
        </a>
    @else
        @foreach(['Mañana', 'Tarde', 'Noche'] as $turno)
            @if($gruposDisponibles->has($turno))
                @foreach($gruposDisponibles[$turno] as $grupo)
                <div class="panel-cup mb-4">
                    <div class="panel-cup-header d-flex justify-content-between align-items-center"
                         style="background: {{ $turno === 'Mañana' ? '#ffc107' : ($turno === 'Tarde' ? '#fd7e14' : '#343a40') }};
                                color: {{ $turno === 'Noche' ? '#fff' : '#000' }}">
                        <strong><i class="bi bi-{{ $turno === 'Mañana' ? 'sun' : ($turno === 'Tarde' ? 'sunset' : 'moon') }} me-1"></i>
                            Turno {{ $turno }} — {{ $grupo->codigo }} ({{ $grupo->horario->dias ?? '' }})
                        </strong>
                        <span class="badge {{ $grupo->tieneCupo() ? 'bg-success' : 'bg-danger' }}">
                            Cupos: {{ $grupo->inscritos_actuales }}/{{ $grupo->cupo_max }}
                        </span>
                    </div>
                    <div class="panel-cup-body p-0">
                        <table class="table table-bordered mb-0">
                            <thead class="table-dark">
                                <tr><th>#</th><th>Materia</th><th>Horario</th><th>Aula</th><th>Docente</th></tr>
                            </thead>
                            <tbody>
                                @foreach($grupo->grupoMaterias->sortBy('orden') as $gm)
                                <tr>
                                    <td>{{ $gm->orden }}</td>
                                    <td>{{ $gm->materia->nombre ?? '—' }}</td>
                                    <td>{{ $gm->rango }}</td>
                                    <td>
                                        @php $aulaEf = $gm->aula ?? $grupo->aula; @endphp
                                        {{ $aulaEf ? $aulaEf->edificio . ' ' . $aulaEf->codigo : '—' }}
                                    </td>
                                    <td>{{ $gm->docente->persona->nombre ?? 'Por asignar' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">
                        @if($grupo->tieneCupo())
                        <form action="{{ route('grupos.confirmar', $inscripcion) }}" method="POST">
                            @csrf
                            <input type="hidden" name="grupo_id" value="{{ $grupo->id }}">
                            <button class="btn btn-primary"
                                    onclick="return confirm('¿Confirmar este turno? No podrás cambiarlo después.')">
                                Elegir este turno
                            </button>
                        </form>
                        @else
                        <span class="badge bg-danger">Sin cupo</span>
                        @endif
                    </div>
                </div>
                @endforeach
            @endif
        @endforeach

        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver a mi panel
        </a>
    @endif
@endif

@endsection