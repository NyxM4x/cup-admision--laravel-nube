@extends('layouts.base')
@section('titulo', 'Editar Grupo — CUP')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-pencil-square me-2"></i>Editar Grupo</h1>
    <p class="page-subtitle">
      <span class="badge-cup badge-modulo">{{ $grupo->codigo }}</span>
      Turno: {{ $grupo->horario?->turno ?? '—' }} — {{ $grupo->horario?->rango ?? '' }}
    </p>
  </div>
  <a href="{{ route('grupos.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

@if($errors->any())
  <div class="alert alert-danger">
    <strong>Corrige los errores:</strong>
    <ul class="mb-0 mt-1">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
  </div>
@endif

<form action="{{ route('grupos.update', $grupo) }}" method="POST">
  @csrf @method('PUT')

  {{-- CABECERA --}}
  <div class="panel-cup mb-4">
    <div class="panel-cup-header">
      <strong><i class="bi bi-info-circle me-1"></i> Datos del Grupo</strong>
    </div>
    <div class="panel-cup-body">
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label fw-semibold">Código <span class="text-danger">*</span></label>
          <input type="text" name="codigo" value="{{ old('codigo', $grupo->codigo) }}"
                 class="form-control @error('codigo') is-invalid @enderror">
          @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Periodo</label>
          <input type="text"
                 value="Periodo #{{ $grupo->periodo_id }}{{ $grupo->periodo?->activo ? ' (activo)' : '' }}"
                 class="form-control" readonly disabled>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Turno (Horario) <span class="text-danger">*</span></label>
          <select name="horario_id" class="form-select @error('horario_id') is-invalid @enderror">
            <option value="">— Seleccionar —</option>
            @foreach($horarios as $h)
              <option value="{{ $h->id }}"
                      {{ old('horario_id', $grupo->horario_id) == $h->id ? 'selected' : '' }}>
                {{ $h->turno }} — {{ $h->rango }} ({{ $h->dias }})
              </option>
            @endforeach
          </select>
          @error('horario_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Cupo máximo <span class="text-danger">*</span></label>
          <input type="number" name="cupo_max" value="{{ old('cupo_max', $grupo->cupo_max) }}"
                 class="form-control @error('cupo_max') is-invalid @enderror" min="1" max="70">
          <small class="text-muted">Máximo permitido: 70 alumnos por grupo</small>       
          @error('cupo_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Aula por defecto</label>
          <select name="aula_id" class="form-select">
            <option value="">— Sin aula —</option>
            @foreach($aulas as $aula)
              <option value="{{ $aula->id }}"
                      {{ old('aula_id', $grupo->aula_id) == $aula->id ? 'selected' : '' }}>
                {{ $aula->codigo }} (cap. {{ $aula->capacidad }})
              </option>
            @endforeach
          </select>
          <small class="text-muted">Puede sobreescribirse por materia abajo</small>
        </div>
      </div>
    </div>
  </div>

  {{-- BLOQUES DE MATERIAS --}}
  <div class="panel-cup mb-4">
    <div class="panel-cup-header d-flex justify-content-between align-items-center">
      <strong><i class="bi bi-grid me-1"></i> Materias del grupo</strong>
      <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarBloque">
        <i class="bi bi-plus-circle me-1"></i> Agregar materia
      </button>
    </div>
    <div class="panel-cup-body" id="contenedorBloques">
      @php
        $bloquesPrev = old('bloques');
        if ($bloquesPrev) {
          $bloquesRender = collect($bloquesPrev)->map(fn($b) => (array)$b)->toArray();
        } else {
          $bloquesRender = $grupo->grupoMaterias->map(fn($gm) => [
            'materia_id'  => $gm->materia_id,
            'docente_id'  => $gm->docente_id,
            'hora_inicio' => $gm->hora_inicio ? substr($gm->hora_inicio, 0, 5) : '',
            'hora_fin'    => $gm->hora_fin    ? substr($gm->hora_fin,    0, 5) : '',
            'aula_id'     => $gm->aula_id,
          ])->toArray();
        }
      @endphp

      @foreach($bloquesRender as $i => $bloque)
        @include('grupos._bloque', ['i' => $i, 'bloque' => $bloque])
      @endforeach
    </div>
    @error('bloques')
      <div class="alert alert-warning m-3">{{ $message }}</div>
    @enderror
  </div>

  <div class="d-flex gap-2 justify-content-end">
    <a href="{{ route('grupos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    <button type="submit" class="btn btn-cup-primary">
      <i class="bi bi-check2-circle me-1"></i> Guardar Cambios
    </button>
  </div>
</form>

<template id="templateBloque">
  @include('grupos._bloque', ['i' => '__INDEX__', 'bloque' => []])
</template>

@endsection

@push('scripts')
<script>
const docentesPorMateria = @json($docentesPorMateria);
let bloqueCount = {{ count($bloquesRender ?? []) }};

document.getElementById('btnAgregarBloque').addEventListener('click', agregarBloque);

function agregarBloque() {
  const template = document.getElementById('templateBloque').innerHTML;
  const html     = template.replace(/__INDEX__/g, bloqueCount);
  const wrapper  = document.createElement('div');
  wrapper.innerHTML = html;
  document.getElementById('contenedorBloques').appendChild(wrapper.firstElementChild);
  bloqueCount++;
  actualizarNumeracion();
}

function eliminarBloque(btn) {
  if (document.querySelectorAll('#contenedorBloques .bloque-materia').length <= 1) {
    alert('El grupo debe tener al menos una materia.');
    return;
  }
  btn.closest('.bloque-materia').remove();
  actualizarNumeracion();
}

// ── DELEGACIÓN: escucha todos los selects de materia desde el contenedor ──
document.getElementById('contenedorBloques').addEventListener('change', function(e) {
  if (!e.target.classList.contains('select-materia')) return;

  const bloque    = e.target.closest('.bloque-materia');
  const selectDoc = bloque.querySelector('.select-docente');
  const sigla     = e.target.options[e.target.selectedIndex]?.dataset?.sigla ?? '';

  selectDoc.innerHTML = '<option value="">— Sin docente —</option>';

  if (sigla && docentesPorMateria[sigla]) {
    docentesPorMateria[sigla].forEach(d => {
      const opt       = document.createElement('option');
      opt.value       = d.id;
      opt.textContent = d.nombre;
      selectDoc.appendChild(opt);
    });
  }
});

function actualizarNumeracion() {
  document.querySelectorAll('#contenedorBloques .bloque-materia').forEach((el, i) => {
    const span = el.querySelector('.num-bloque');
    if (span) span.textContent = i + 1;
  });
}
</script>
@endpush