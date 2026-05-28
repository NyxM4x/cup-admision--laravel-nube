@extends('layouts.base')

@section('titulo', 'Editar Materia')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-book me-2"></i>Editar Materia: {{ $materia->nombre }}</h1>
    <p class="page-subtitle">Modificar la materia y su configuración de evaluación</p>
  </div>
  <a href="{{ route('materias.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

@if($errors->any())
  <div class="alert alert-cup-danger">
    <i class="bi bi-exclamation-triangle me-2"></i>
    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
  </div>
@endif

<div class="panel-cup" style="max-width:720px">
  <div class="panel-cup-body">
    <form action="{{ route('materias.update', $materia) }}" method="POST">
      @csrf @method('PUT')

      <div class="mb-3">
        <label class="form-label">Sigla</label>
        <input type="text" name="sigla" class="form-control text-uppercase" value="{{ $materia->sigla }}" required maxlength="20">
      </div>
      <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" value="{{ $materia->nombre }}" required maxlength="100">
      </div>
      <div class="mb-4">
        <label class="form-label">Días de dictado</label>
        <select name="dias" class="form-select" required>
          <option value="LMV" {{ $materia->dias == 'LMV' ? 'selected' : '' }}>LMV (Lunes, Miércoles, Viernes)</option>
          <option value="MJ"  {{ $materia->dias == 'MJ'  ? 'selected' : '' }}>MJ (Martes, Jueves)</option>
        </select>
      </div>

      <hr>
      <h6 class="mb-3" style="color:var(--cup-primary-light);font-weight:600;">
        <i class="bi bi-bar-chart me-2"></i>Pesos de exámenes <small class="text-muted">(deben sumar 100%)</small>
      </h6>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Examen 1 (%)</label>
          <input type="number" name="peso_examen1" class="form-control" value="{{ $materia->peso_examen1 }}" required min="1" max="98" step="0.01" oninput="calcularSuma()">
        </div>
        <div class="col-md-4">
          <label class="form-label">Examen 2 (%)</label>
          <input type="number" name="peso_examen2" class="form-control" value="{{ $materia->peso_examen2 }}" required min="1" max="98" step="0.01" oninput="calcularSuma()">
        </div>
        <div class="col-md-4">
          <label class="form-label">Examen 3 (%)</label>
          <input type="number" name="peso_examen3" class="form-control" value="{{ $materia->peso_examen3 }}" required min="1" max="98" step="0.01" oninput="calcularSuma()">
        </div>
      </div>
      <div id="suma-pesos" class="alert alert-success py-2 mt-3">
        Total: <strong id="total-pesos">100</strong>%
        <span id="suma-ok">✅ Correcto</span>
      </div>

      <hr>
      <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('materias.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-cup-primary">
          <i class="bi bi-check-circle me-1"></i> Actualizar Materia
        </button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
function calcularSuma() {
    const p1 = parseFloat(document.querySelector('[name=peso_examen1]').value) || 0;
    const p2 = parseFloat(document.querySelector('[name=peso_examen2]').value) || 0;
    const p3 = parseFloat(document.querySelector('[name=peso_examen3]').value) || 0;
    const total = p1 + p2 + p3;
    document.getElementById('total-pesos').textContent = total.toFixed(2);
    const div = document.getElementById('suma-pesos');
    const ok  = document.getElementById('suma-ok');
    if (Math.abs(total - 100) < 0.01) {
        div.className = 'alert alert-success py-2 mt-3';
        ok.textContent = '✅ Correcto';
    } else {
        div.className = 'alert alert-danger py-2 mt-3';
        ok.textContent = '❌ Debe sumar 100%';
    }
}
document.addEventListener('DOMContentLoaded', calcularSuma);
</script>
@endpush
