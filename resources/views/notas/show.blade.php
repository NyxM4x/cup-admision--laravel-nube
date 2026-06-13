@extends('layouts.base')
@section('titulo', 'Notas — {{ $grupo->codigo }}')
@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1><i class="bi bi-journal-check me-2"></i>Notas — {{ $grupo->codigo }}</h1>
        <p class="page-subtitle">
            Turno {{ $grupo->horario?->turno }} —
            {{ $grupo->inscritos_actuales }} postulantes inscritos
        </p>
    </div>
    <a href="{{ route('notas.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    </div>
@endif

@if($grupo->postulantes->isEmpty())
    <div class="alert alert-warning">
        <i class="bi bi-people me-2"></i>
        Este grupo aún no tiene postulantes inscritos.
    </div>
@else

{{-- Una pestaña por cada materia (GrupoMateria) --}}
<ul class="nav nav-tabs mb-3" id="tabsMaterias">
    @foreach($grupo->grupoMaterias as $i => $gm)
        <li class="nav-item">
            <button class="nav-link {{ $i === 0 ? 'active' : '' }}"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-gm-{{ $gm->id }}">
                <span class="badge bg-secondary me-1">{{ $gm->materia?->sigla }}</span>
                {{ $gm->materia?->nombre }}
                <small class="text-muted ms-1">{{ $gm->rango }}</small>
            </button>
        </li>
    @endforeach
</ul>

<div class="tab-content">
    @foreach($grupo->grupoMaterias as $i => $gm)
        <div class="tab-pane fade {{ $i === 0 ? 'show active' : '' }}"
             id="tab-gm-{{ $gm->id }}">

            <div class="panel-cup mb-4">
                <div class="panel-cup-header d-flex justify-content-between align-items-center">
                    <strong>
                        <i class="bi bi-book me-1"></i>
                        {{ $gm->materia?->nombre }} ({{ $gm->materia?->sigla }})
                        — Docente: {{ $gm->docente?->persona?->nombre ?? 'Sin asignar' }}
                    </strong>
                    <span class="badge bg-info text-dark">{{ $gm->rango }}</span>
                </div>

                {{-- Formulario masivo para este bloque --}}
                <form action="{{ route('notas.masivo', $gm) }}" method="POST">
                    @csrf

                    <div class="panel-cup-body p-0">
                        <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0 align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th width="40">#</th>
                                    <th>Postulante</th>
                                    <th>CI</th>
                                    <th class="text-center" width="100">
                                        Examen 1<br><small class="text-muted fw-normal">/100</small>
                                    </th>
                                    <th class="text-center" width="100">
                                        Examen 2<br><small class="text-muted fw-normal">/100</small>
                                    </th>
                                    <th class="text-center" width="100">
                                        Examen 3<br><small class="text-muted fw-normal">/100</small>
                                    </th>
                                    <th class="text-center" width="100">Nota Final</th>
                                    <th class="text-center" width="110">Resultado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($grupo->postulantes->sortBy('persona.nombre') as $idx => $postulante)
                                    @php
                                        $nota = $notasPorBloque[$gm->id][$postulante->id] ?? null;
                                    @endphp
                                    <tr>
                                        <td class="text-center text-muted">{{ $idx + 1 }}</td>
                                        <td>
                                            <strong>{{ $postulante->persona?->nombre ?? '—' }}</strong>
                                            <input type="hidden"
                                                   name="notas[{{ $idx }}][postulante_id]"
                                                   value="{{ $postulante->id }}">
                                        </td>
                                        <td class="text-muted small">
                                            {{ $postulante->persona?->ci ?? '—' }}
                                        </td>
                                        <td class="text-center p-1">
                                            <input type="number"
                                                   name="notas[{{ $idx }}][examen1]"
                                                   value="{{ $nota?->examen1 }}"
                                                   class="form-control form-control-sm text-center nota-input"
                                                   min="0" max="100" step="0.01"
                                                   placeholder="—"
                                                   data-idx="{{ $idx }}">
                                        </td>
                                        <td class="text-center p-1">
                                            <input type="number"
                                                   name="notas[{{ $idx }}][examen2]"
                                                   value="{{ $nota?->examen2 }}"
                                                   class="form-control form-control-sm text-center nota-input"
                                                   min="0" max="100" step="0.01"
                                                   placeholder="—"
                                                   data-idx="{{ $idx }}">
                                        </td>
                                        <td class="text-center p-1">
                                            <input type="number"
                                                   name="notas[{{ $idx }}][examen3]"
                                                   value="{{ $nota?->examen3 }}"
                                                   class="form-control form-control-sm text-center nota-input"
                                                   min="0" max="100" step="0.01"
                                                   placeholder="—"
                                                   data-idx="{{ $idx }}">
                                        </td>
                                        {{-- Nota final y resultado — calculados por CU22 --}}
                                        <td class="text-center fw-bold" id="final-{{ $gm->id }}-{{ $idx }}">
                                            @if($nota?->nota_final !== null)
                                                {{ number_format($nota->nota_final, 2) }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center" id="resultado-{{ $gm->id }}-{{ $idx }}">
                                            @if($nota)
                                                {!! $nota->badge_resultado !!}
                                            @else
                                                <span class="badge bg-secondary">Pendiente</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                    </div>

                    <div class="p-3 d-flex justify-content-between align-items-center border-top">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Nota mínima de aprobación: <strong>{{ \App\Models\Nota::NOTA_MINIMA }}/100</strong>
                            — La nota final se calcula como promedio de los 3 exámenes.
                        </small>
                        <button type="submit" class="btn btn-cup-primary">
                            <i class="bi bi-check2-circle me-1"></i>
                            Guardar notas de {{ $gm->materia?->sigla }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endforeach
</div>

{{-- Resumen general del grupo --}}
<div class="panel-cup mt-2">
    <div class="panel-cup-header">
        <strong><i class="bi bi-bar-chart me-1"></i> Resumen del grupo</strong>
    </div>
    <div class="panel-cup-body">
        <div class="row g-3 text-center">
            @foreach($grupo->grupoMaterias as $gm)
                @php
                    $notasBloque   = $notasPorBloque[$gm->id] ?? collect();
                    $aprobados     = $notasBloque->where('resultado', 'aprobado')->count();
                    $reprobados    = $notasBloque->where('resultado', 'reprobado')->count();
                    $pendientes    = $grupo->postulantes->count() - $notasBloque->count();
                    $promedio      = $notasBloque->whereNotNull('nota_final')->avg('nota_final');
                @endphp
                <div class="col-md-3">
                    <div class="border rounded p-3">
                        <strong class="d-block">{{ $gm->materia?->sigla }}</strong>
                        <small class="text-muted">{{ $gm->materia?->nombre }}</small>
                        <div class="mt-2">
                            <span class="badge bg-success me-1">{{ $aprobados }} aprobados</span>
                            <span class="badge bg-danger me-1">{{ $reprobados }} reprobados</span>
                            <span class="badge bg-secondary">{{ $pendientes }} pendientes</span>
                        </div>
                        @if($promedio !== null)
                            <div class="mt-1">
                                <small class="text-muted">
                                    Promedio: <strong>{{ number_format($promedio, 2) }}</strong>
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@endif

@endsection