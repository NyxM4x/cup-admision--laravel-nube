@php $g = $grupo ?? null; $esEdit = (bool) $g; @endphp

<div class="row g-3 mb-3">
  <div class="col-md-4">
    <label class="form-label">Código <span class="text-danger">*</span></label>
    <input type="text" name="codigo" class="form-control" maxlength="20" required
           value="{{ old('codigo', $g->codigo ?? '') }}" placeholder="ej: G-1-MAT-1">
  </div>
  <div class="col-md-4">
    <label class="form-label">Periodo <span class="text-danger">*</span></label>
    @if($esEdit)
      <input type="text" class="form-control" value="Periodo #{{ $g->periodo_id }}" disabled>
    @else
      <select name="periodo_id" class="form-select" required>
        @foreach($periodos as $per)
          <option value="{{ $per->id }}" {{ (int)old('periodo_id', optional($periodos->firstWhere('activo', true))->id) === (int)$per->id ? 'selected' : '' }}>
            Periodo #{{ $per->id }} {{ $per->activo ? '(activo)' : '(cerrado)' }}
          </option>
        @endforeach
      </select>
    @endif
  </div>
  <div class="col-md-4">
    <label class="form-label">Materia <span class="text-danger">*</span></label>
    @if($esEdit)
      <input type="text" class="form-control" value="{{ $g->materia->sigla ?? '' }} — {{ $g->materia->nombre ?? '' }}" disabled>
    @else
      <select name="materia_id" class="form-select" required>
        <option value="">— Seleccionar —</option>
        @foreach($materias as $m)
          <option value="{{ $m->id }}" {{ (int)old('materia_id') === (int)$m->id ? 'selected' : '' }}>{{ $m->sigla }} — {{ $m->nombre }}</option>
        @endforeach
      </select>
    @endif
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-4">
    <label class="form-label">Horario</label>
    <select name="horario_id" class="form-select">
      <option value="">— Sin horario —</option>
      @foreach($horarios as $h)
        <option value="{{ $h->id }}" {{ (int)old('horario_id', $g->horario_id ?? 0) === (int)$h->id ? 'selected' : '' }}>
          {{ $h->codigo }} · {{ $h->turno }} {{ $h->rango }}
        </option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label">Aula</label>
    <select name="aula_id" class="form-select">
      <option value="">— Sin aula —</option>
      @foreach($aulas as $a)
        <option value="{{ $a->id }}" {{ (int)old('aula_id', $g->aula_id ?? 0) === (int)$a->id ? 'selected' : '' }}>
          {{ $a->codigo }} (cap. {{ $a->capacidad }})
        </option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label">Docente</label>
    <select name="docente_id" class="form-select">
      <option value="">— Sin docente —</option>
      @foreach($docentes as $d)
        <option value="{{ $d->id }}" {{ (int)old('docente_id', $g->docente_id ?? 0) === (int)$d->id ? 'selected' : '' }}>
          {{ $d->persona->nombre ?? ('Docente #'.$d->id) }}
        </option>
      @endforeach
    </select>
  </div>
</div>

<div class="row g-3 mb-2">
  <div class="col-md-4">
    <label class="form-label">Cupo máximo <span class="text-danger">*</span></label>
    <input type="number" name="cupo_max" class="form-control" min="1" max="300" required
           value="{{ old('cupo_max', $g->cupo_max ?? 70) }}">
  </div>
</div>
