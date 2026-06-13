{{--
  Partial: grupos/_bloque.blade.php
  Variables: $i (índice o '__INDEX__'), $bloque (array valores), $materias, $aulas, $docentesPorMateria
--}}

@php
  $materiaSelId = $bloque['materia_id']  ?? null;
  $docenteSelId = $bloque['docente_id']  ?? null;
  $aulaSelId    = $bloque['aula_id']     ?? null;
  $horaIni      = $bloque['hora_inicio'] ?? '';
  $horaFin      = $bloque['hora_fin']    ?? '';

  $siglaSel = null;
  if ($materiaSelId) {
    $siglaSel = $materias->firstWhere('id', $materiaSelId)?->sigla;
  }
@endphp

<div class="bloque-materia border rounded p-3 mb-3 bg-light">

  <div class="d-flex justify-content-between align-items-center mb-2">
    <span class="fw-semibold text-muted small">
      <i class="bi bi-book me-1"></i> Bloque #<span class="num-bloque">{{ is_int($i) ? $i + 1 : '?' }}</span>
    </span>
    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarBloque(this)">
      <i class="bi bi-trash3"></i>
    </button>
  </div>

  <div class="row g-2">

    {{-- MATERIA --}}
    <div class="col-md-3">
      <label class="form-label small fw-semibold">Materia <span class="text-danger">*</span></label>
      <select name="bloques[{{ $i }}][materia_id]"
              class="form-select form-select-sm select-materia"
              required>
        <option value="">— Seleccionar —</option>
        @foreach($materias as $mat)
          <option value="{{ $mat->id }}"
                  data-sigla="{{ $mat->sigla }}"
                  {{ (string)$materiaSelId === (string)$mat->id ? 'selected' : '' }}>
            {{ $mat->sigla }} — {{ $mat->nombre }}
          </option>
        @endforeach
      </select>
    </div>

    {{-- DOCENTE (filtrado por materia) --}}
    <div class="col-md-3">
      <label class="form-label small fw-semibold">Docente</label>
      <select name="bloques[{{ $i }}][docente_id]"
              class="form-select form-select-sm select-docente">
        <option value="">— Sin docente —</option>
        {{-- Se carga la lista inicial si hay materia preseleccionada (edit/old) --}}
        @if($siglaSel && isset($docentesPorMateria[$siglaSel]))
          @foreach($docentesPorMateria[$siglaSel] as $doc)
            <option value="{{ $doc['id'] }}"
                    {{ (string)$docenteSelId === (string)$doc['id'] ? 'selected' : '' }}>
              {{ $doc['nombre'] }}
            </option>
          @endforeach
        @endif
      </select>
      <small class="text-muted">Filtra al elegir materia</small>
    </div>

    {{-- HORA INICIO --}}
    <div class="col-md-2">
      <label class="form-label small fw-semibold">Hora inicio <span class="text-danger">*</span></label>
      <input type="time"
             name="bloques[{{ $i }}][hora_inicio]"
             value="{{ $horaIni }}"
             class="form-control form-control-sm"
             required>
    </div>

    {{-- HORA FIN --}}
    <div class="col-md-2">
      <label class="form-label small fw-semibold">Hora fin <span class="text-danger">*</span></label>
      <input type="time"
             name="bloques[{{ $i }}][hora_fin]"
             value="{{ $horaFin }}"
             class="form-control form-control-sm"
             required>
    </div>

    {{-- AULA ESPECÍFICA --}}
    <div class="col-md-2">
      <label class="form-label small fw-semibold">Aula (opcional)</label>
      <select name="bloques[{{ $i }}][aula_id]"
              class="form-select form-select-sm">
        <option value="">— Hereda del grupo —</option>
        @foreach($aulas as $aula)
          <option value="{{ $aula->id }}"
                  {{ (string)$aulaSelId === (string)$aula->id ? 'selected' : '' }}>
            {{ $aula->codigo }} ({{ $aula->capacidad }})
          </option>
        @endforeach
      </select>
    </div>

  </div>
</div>