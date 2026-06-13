@extends('layouts.base')

@section('titulo', 'Grupos')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-people-fill me-2"></i>Grupos</h1>
    <p class="page-subtitle">Grupos por turno (Mañana/Tarde) — máx. {{ \App\Http\Controllers\GrupoController::CUPO_DEFAULT }} alumnos por grupo</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('grupos.generar-automaticos.form') }}" class="btn btn-outline-primary">
      <i class="bi bi-magic me-1"></i> Generar automáticos
    </a>
    <a href="{{ route('grupos.create') }}" class="btn btn-cup-primary">
      <i class="bi bi-plus-circle me-1"></i> Nuevo Grupo
    </a>
  </div>
</div>

{{-- Mensajes flash --}}
@if(session('success'))
  <div class="alert alert-success d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
  </div>
@endif
@if(session('error'))
  <div class="alert alert-danger d-flex align-items-center gap-2">
    <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
  </div>
@endif

{{-- Filtros --}}
<form method="GET" class="row g-2 align-items-center mb-3">
  <div class="col-md-6">
    <div class="input-group">
      <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
      <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="Buscar por código o turno...">
    </div>
  </div>
  <div class="col-md-4">
    <select name="periodo_id" class="form-select">
      <option value="todos" {{ (string)($periodoId ?? '') === 'todos' ? 'selected' : '' }}>Todos los periodos</option>
      @foreach($periodos as $per)
        <option value="{{ $per->id }}" {{ (int)($periodoId ?? 0) === (int)$per->id ? 'selected' : '' }}>
          Periodo #{{ $per->id }} {{ $per->activo ? '(activo)' : '(cerrado)' }}
        </option>
      @endforeach
    </select>
  </div>
  <div class="col-md-2">
    <button type="submit" class="btn btn-cup-primary w-100"><i class="bi bi-funnel me-1"></i> Filtrar</button>
  </div>
</form>

{{-- ══ TABLA DE GRUPOS ══ --}}
<div class="panel-cup">
  <div class="panel-cup-body p-0">
    <div class="table-responsive">
    <table class="table-cup table mb-0 align-middle">
      <thead>
        <tr>
          <th>Código</th>
          <th>Turno</th>
          <th>Aula por defecto</th>
          <th>Materias configuradas</th>
          <th class="text-center">Cupo</th>
          <th>Estado</th>
          <th class="text-end">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($grupos as $grupo)
          <tr class="{{ $grupo->activo ? '' : 'table-secondary' }}">

            {{-- Código --}}
            <td><span class="badge-cup badge-modulo">{{ $grupo->codigo }}</span></td>

            {{-- Turno (antes era Materia + Docente) --}}
            <td>
              @if($grupo->horario)
                <strong>{{ $grupo->horario->turno }}</strong>
                <small class="d-block text-muted">{{ $grupo->horario->rango }}</small>
              @else
                <small class="text-muted">Sin horario</small>
              @endif
            </td>

            {{-- CU20: Aula por defecto (asignación rápida inline) --}}
            <td style="min-width:180px">
              <form action="{{ route('grupos.asignar-aula', $grupo) }}" method="POST" class="d-flex gap-1">
                @csrf
                <select name="aula_id" class="form-select form-select-sm" required>
                  <option value="">— Aula —</option>
                  @foreach($aulas as $aula)
                    <option value="{{ $aula->id }}" {{ $grupo->aula_id === $aula->id ? 'selected' : '' }}>
                      {{ $aula->codigo }} (cap. {{ $aula->capacidad }})
                    </option>
                  @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-outline-primary" title="Asignar aula por defecto">
                  <i class="bi bi-check2"></i>
                </button>
              </form>
            </td>

            {{-- Materias configuradas (resumen de los bloques del grupo) --}}
            <td style="min-width:220px">
              @if($grupo->grupoMaterias->isEmpty())
                <span class="badge bg-warning text-dark">
                  <i class="bi bi-exclamation-triangle me-1"></i>Sin materias
                </span>
                <a href="{{ route('grupos.edit', $grupo) }}" class="btn btn-xs btn-link ms-1 p-0">
                  Configurar
                </a>
              @else
                <div class="d-flex flex-column gap-1">
                  @foreach($grupo->grupoMaterias->sortBy('orden') as $gm)
                    <div class="d-flex align-items-center gap-1">
                      <span class="badge bg-secondary" style="font-size:0.7rem;min-width:38px">
                        {{ $gm->materia?->sigla ?? '?' }}
                      </span>
                      <small class="text-muted">
                        @if($gm->hora_inicio)
                          {{ substr($gm->hora_inicio, 0, 5) }}–{{ substr($gm->hora_fin, 0, 5) }}
                        @endif
                        · {{ $gm->docente?->persona?->nombre ?? '— sin docente' }}
                      </small>
                    </div>
                  @endforeach
                </div>
              @endif
            </td>

            {{-- Cupo --}}
            <td class="text-center">
              @php
                $pct = $grupo->cupo_max > 0
                  ? round(($grupo->inscritos_actuales / $grupo->cupo_max) * 100)
                  : 0;
              @endphp
              <span class="badge {{ $grupo->inscritos_actuales >= $grupo->cupo_max ? 'bg-danger' : 'bg-secondary' }}">
                {{ $grupo->inscritos_actuales }} / {{ $grupo->cupo_max }}
              </span>
              <div class="progress mt-1" style="height:4px;min-width:60px">
                <div class="progress-bar {{ $pct >= 90 ? 'bg-danger' : ($pct >= 60 ? 'bg-warning' : 'bg-success') }}"
                     style="width:{{ $pct }}%"></div>
              </div>
            </td>

            {{-- Estado --}}
            <td>
              @if($grupo->activo)
                <span class="badge-cup badge-activo">Activo</span>
              @else
                <span class="badge-cup badge-inactivo">Archivado</span>
              @endif
            </td>

            {{-- Acciones --}}
            <td class="text-end" style="min-width:110px">
              <a href="{{ route('grupos.edit', $grupo) }}" class="btn-action btn-action-edit" title="Editar materias y horarios">
                <i class="bi bi-pencil"></i>
              </a>
              @if($grupo->activo)
                <form id="form-archivar-grupo-{{ $grupo->id }}"
                      action="{{ route('grupos.archivar', $grupo) }}" method="POST" style="display:inline">
                  @csrf
                  <button type="button" class="btn-action btn-action-danger" title="Archivar"
                          onclick="cupConfirmar({
                            titulo: 'Archivar grupo',
                            mensaje: '¿Querés archivar el grupo {{ addslashes($grupo->codigo) }}?',
                            subtexto: 'No se elimina; podés reactivarlo después.',
                            textoBoton: 'Sí, archivar',
                            tipo: 'warning',
                            formSelector: '#form-archivar-grupo-{{ $grupo->id }}'
                          })">
                    <i class="bi bi-archive"></i>
                  </button>
                </form>
              @else
                <form id="form-reactivar-grupo-{{ $grupo->id }}"
                      action="{{ route('grupos.reactivar', $grupo) }}" method="POST" style="display:inline">
                  @csrf
                  <button type="button" class="btn-action btn-action-success" title="Reactivar"
                          onclick="cupConfirmar({
                            titulo: 'Reactivar grupo',
                            mensaje: '¿Querés reactivar el grupo {{ addslashes($grupo->codigo) }}?',
                            textoBoton: 'Sí, reactivar',
                            tipo: 'success',
                            formSelector: '#form-reactivar-grupo-{{ $grupo->id }}'
                          })">
                    <i class="bi bi-arrow-counterclockwise"></i>
                  </button>
                </form>
              @endif
            </td>

          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center py-4 text-muted">
              No hay grupos para este filtro.
              Usá "Generar automáticos" para crearlos o "Nuevo Grupo" para crear uno manualmente.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
    </div>
  </div>
</div>

@if($grupos->hasPages())
  <div class="mt-3 d-flex justify-content-center">
    {{ $grupos->links() }}
  </div>
@endif

@endsection