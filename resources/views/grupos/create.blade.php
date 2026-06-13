@extends('layouts.base')
@section('titulo', 'Nuevo Grupo — CUP')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-people-fill me-2"></i>Nuevo Grupo</h1>
    <p class="page-subtitle">Un grupo representa un turno completo (Mañana o Tarde) con sus 4 materias</p>
  </div>
  <a href="{{ route('grupos.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

@if($errors->any())
  <div class="alert alert-danger">
    <strong>Corrige los siguientes errores:</strong>
    <ul class="mb-0 mt-1">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
  </div>
@endif

<form action="{{ route('grupos.store') }}" method="POST">
  @csrf

  {{-- CABECERA --}}
  <div class="panel-cup mb-4">
    <div class="panel-cup-header">
      <strong><i class="bi bi-info-circle me-1"></i> Datos del Grupo</strong>
    </div>
    <div class="panel-cup-body">
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label fw-semibold">Código <span class="text-danger">*</span></label>
          <input type="text" name="codigo" value="{{ old('codigo') }}"
                 class="form-control @error('codigo') is-invalid @enderror"
                 placeholder="ej: G-MAÑANA-1">
          @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Periodo <span class="text-danger">*</span></label>
          <select name="periodo_id" class="form-select @error('periodo_id') is-invalid @enderror">
            <option value="">— Seleccionar —</option>
            @foreach($periodos as $per)
              <option value="{{ $per->id }}" {{ old('periodo_id') == $per->id ? 'selected' : '' }}>
                Periodo #{{ $per->id }} {{ $per->activo ? '(activo)' : '' }}
              </option>
            @endforeach
          </select>
          @error('periodo_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Turno (Horario) <span class="text-danger">*</span></label>
          <select name="horario_id" class="form-select @error('horario_id') is-invalid @enderror">
            <option value="">— Seleccionar turno —</option>
            @foreach($horarios as $h)
              <option value="{{ $h->id }}" {{ old('horario_id') == $h->id ? 'selected' : '' }}>
                {{ $h->turno }} — {{ $h->rango }} ({{ $h->dias }})
              </option>
            @endforeach
          </select>
          @error('horario_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Cupo máximo <span class="text-danger">*</span></label>
          <input type="number" name="cupo_max" value="{{ old('cupo_max', 70) }}"
                 class="form-control @error('cupo_max') is-invalid @enderror" min="1" max="70">
          <small class="text-muted">Máximo permitido: 70 alumnos por grupo</small>       
          @error('cupo_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Aula por defecto</label>
          <select name="aula_id" class="form-select">
            <option value="">— Sin aula —</option>
            @foreach($aulas as $aula)
              <option value="{{ $aula->id }}" {{ old('aula_id') == $aula->id ? 'selected' : '' }}>
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
      @php $bloquesPrev = old('bloques', []); @endphp
      @forelse($bloquesPrev as $i => $bloque)
        @include('grupos._bloque', ['i' => $i, 'bloque' => $bloque])
      @empty
        {{-- JS agrega el primer bloque al cargar --}}
      @endforelse
    </div>
    @error('bloques')
      <div class="alert alert-warning m-3">{{ $message }}</div>
    @enderror
  </div>

  <div class="d-flex gap-2 justify-content-end">
    <a href="{{ route('grupos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    <button type="submit" class="btn btn-cup-primary">
      <i class="bi bi-check2-circle me-1"></i> Guardar Grupo
    </button>
  </div>
</form>

<template id="templateBloque">
  @include('grupos._bloque', ['i' => '__INDEX__', 'bloque' => []])
</template>

@endsection

@push('scripts')
<script>
// Docentes indexados por sigla de materia { "MAT": [{id,nombre},...], "FIS": [...] }
const docentesPorMateria = @json($docentesPorMateria);

let bloqueCount = {{ count($bloquesPrev) }};

// ── Agregar primer bloque si la página carga vacía ─────────────────
document.addEventListener('DOMContentLoaded', () => {
  if (bloqueCount === 0) agregarBloque();
});

// ── Botón agregar materia ──────────────────────────────────────────
document.getElementById('btnAgregarBloque').addEventListener('click', agregarBloque);

function agregarBloque() {
  const template = document.getElementById('templateBloque').innerHTML;
  const html     = template.replace(/__INDEX__/g, bloqueCount);
  const wrapper  = document.createElement('div');
  wrapper.innerHTML = html;
  document.getElementById('contenedorBloques').appendChild(wrapper.firstElementChild);
  bloqueCount++;
  // Actualizar numeración visible
  actualizarNumeracion();
}

// ── Eliminar bloque ───────────────────────────────────────────────
function eliminarBloque(btn) {
  if (document.querySelectorAll('#contenedorBloques .bloque-materia').length <= 1) {
    alert('El grupo debe tener al menos una materia.');
    return;
  }
  btn.closest('.bloque-materia').remove();
  actualizarNumeracion();
}

// ── Filtrar docentes: delegación en el contenedor ────────────────
// Escucha TODOS los selects de materia con un solo listener
document.getElementById('contenedorBloques').addEventListener('change', function(e) {
  if (!e.target.classList.contains('select-materia')) return;

  const bloque       = e.target.closest('.bloque-materia');
  const selectDoc    = bloque.querySelector('.select-docente');
  const sigla        = e.target.options[e.target.selectedIndex]?.dataset?.sigla ?? '';

  // Limpiar y rellenar docentes
  selectDoc.innerHTML = '<option value="">— Sin docente —</option>';

  if (sigla && docentesPorMateria[sigla]) {
    docentesPorMateria[sigla].forEach(d => {
      const opt = document.createElement('option');
      opt.value       = d.id;
      opt.textContent = d.nombre;
      selectDoc.appendChild(opt);
    });
  }
});

// ── Numeración visual de bloques ──────────────────────────────────
function actualizarNumeracion() {
  document.querySelectorAll('#contenedorBloques .bloque-materia').forEach((el, i) => {
    const span = el.querySelector('.num-bloque');
    if (span) span.textContent = i + 1;
  });
}
</script>
@endpush