@extends('layouts.base')

@section('titulo', 'Nueva Materia')

@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-book me-2"></i>Nueva Materia</h1>
    <p class="page-subtitle">Registrar una materia y su configuración de evaluación</p>
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
    <form action="{{ route('materias.store') }}" method="POST">
      @csrf

      <div class="mb-3">
        <label class="form-label">Sigla <small class="text-muted">(ej: MAT, FIS)</small></label>
        <input type="text" name="sigla" class="form-control text-uppercase" value="{{ old('sigla') }}" required maxlength="20">
      </div>
      <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required maxlength="100">
      </div>
      <hr>
      <h6 class="mb-3" style="color:var(--cup-primary-light);font-weight:600;">
        <i class="bi bi-calendar3 me-2"></i>Horario de la materia
      </h6>

      <div class="mb-3">
        <label class="form-label">Días de dictado <span class="text-danger">*</span></label>
        <div class="row g-2">
          @php
            $diasOpciones = [
              'lunes' => 'Lunes', 'martes' => 'Martes', 'miercoles' => 'Miércoles',
              'jueves' => 'Jueves', 'viernes' => 'Viernes', 'sabado' => 'Sábado',
            ];
            $diasActuales = old('dias_dictado', []);
          @endphp
          @foreach($diasOpciones as $key => $label)
            <div class="col-6 col-md-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="dias_dictado[]" value="{{ $key }}"
                       id="dia_{{ $key }}" {{ in_array($key, $diasActuales) ? 'checked' : '' }}>
                <label class="form-check-label" for="dia_{{ $key }}">{{ $label }}</label>
              </div>
            </div>
          @endforeach
        </div>
        <small class="text-muted">Marcá los días en los que se dicta esta materia.</small>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label">Hora de inicio <span class="text-danger">*</span></label>
          <input type="time" name="hora_inicio" class="form-control" value="{{ old('hora_inicio') }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Hora de fin <span class="text-danger">*</span></label>
          <input type="time" name="hora_fin" class="form-control" value="{{ old('hora_fin') }}" required>
        </div>
      </div>

      <hr>
      <h6 class="mb-3" style="color:var(--cup-primary-light);font-weight:600;">
        <i class="bi bi-bar-chart me-2"></i>Pesos de exámenes <small class="text-muted">(deben sumar 100%)</small>
      </h6>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Examen 1 (%)</label>
          <input type="number" name="peso_examen1" class="form-control" value="{{ old('peso_examen1', 30) }}" required min="1" max="98" step="0.01" oninput="calcularSuma()">
        </div>
        <div class="col-md-4">
          <label class="form-label">Examen 2 (%)</label>
          <input type="number" name="peso_examen2" class="form-control" value="{{ old('peso_examen2', 30) }}" required min="1" max="98" step="0.01" oninput="calcularSuma()">
        </div>
        <div class="col-md-4">
          <label class="form-label">Examen 3 (%)</label>
          <input type="number" name="peso_examen3" class="form-control" value="{{ old('peso_examen3', 40) }}" required min="1" max="98" step="0.01" oninput="calcularSuma()">
        </div>
      </div>
      <div id="suma-pesos" class="alert alert-info py-2 mt-3">
        Total: <strong id="total-pesos">100</strong>%
        <span id="suma-ok">✅ Correcto</span>
      </div>

      <hr>
      <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('materias.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-cup-primary">
          <i class="bi bi-check-circle me-1"></i> Guardar Materia
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
