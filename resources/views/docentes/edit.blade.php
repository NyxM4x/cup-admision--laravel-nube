@extends('layouts.base')
@section('titulo', 'Editar Docente')
@section('contenido')

<div class="page-header d-flex justify-content-between align-items-start mb-4">
  <div>
    <h1><i class="bi bi-pencil-square me-2"></i>Editar Docente: {{ $docente->persona->nombre }}</h1>
    <p class="page-subtitle">Modificar datos del docente</p>
  </div>
  <a href="{{ route('docentes.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

@if($errors->any())
  <div class="alert alert-cup-danger">
    <i class="bi bi-exclamation-triangle me-2"></i>
    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
  </div>
@endif

<div class="panel-cup" style="max-width:900px">
  <div class="panel-cup-body">
    <form action="{{ route('docentes.update', $docente) }}" method="POST" enctype="multipart/form-data">
      @csrf @method('PUT')

      <h5 class="mb-3" style="color:var(--cup-primary-light);font-weight:600;">
        <i class="bi bi-clipboard me-2"></i>Datos Personales
      </h5>

      <div class="row g-3 mb-3">
        <div class="col-md-4">
          <label class="form-label">CI <span class="text-danger">*</span></label>
          <input type="text" name="ci" class="form-control" value="{{ old('ci', $docente->persona->ci) }}" required maxlength="20">
        </div>
        <div class="col-md-8">
          <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
          <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $docente->persona->nombre) }}" required maxlength="200">
        </div>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label">Fecha de nacimiento</label>
          <input type="date" name="fecha_nacimiento" class="form-control"
                 value="{{ old('fecha_nacimiento', optional($docente->persona->fecha_nacimiento)->format('Y-m-d')) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Sexo</label>
          <select name="sexo" class="form-select">
            <option value="">— Seleccionar —</option>
            <option value="M" {{ old('sexo', $docente->persona->sexo) == 'M' ? 'selected' : '' }}>Masculino</option>
            <option value="F" {{ old('sexo', $docente->persona->sexo) == 'F' ? 'selected' : '' }}>Femenino</option>
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Dirección</label>
        <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $docente->persona->direccion) }}" maxlength="255">
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label">Teléfono</label>
          <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $docente->persona->telefono) }}" maxlength="20">
        </div>
        <div class="col-md-6">
          <label class="form-label">Correo electrónico</label>
          <input type="email" name="correo" class="form-control" value="{{ old('correo', $docente->persona->correo) }}" maxlength="150">
        </div>
      </div>

      <hr>

      <h5 class="mb-3" style="color:var(--cup-primary-light);font-weight:600;">
        <i class="bi bi-mortarboard me-2"></i>Datos Académicos
      </h5>

      @php
        $materiasActuales = old('materias',
          $docente->docenteMaterias->count() > 0
            ? $docente->docenteMaterias->pluck('materia_sigla')->toArray()
            : ($docente->materia ? [$docente->materia] : [])
        );
      @endphp

      <div class="row g-3 mb-3">
        <div class="col-md-5">
          <label class="form-label">Profesión</label>
          <select name="profesion_id" id="sel-profesion" class="form-select">
            <option value="" data-materia="">— Sin especificar —</option>
            @foreach($profesiones as $prof)
              <option value="{{ $prof->id }}"
                      data-materia="{{ $prof->materia_sigla }}"
                      {{ old('profesion_id', $docente->profesion_id) == $prof->id ? 'selected' : '' }}>
                {{ $prof->nombre }} {{ $prof->nivel_jerarquico ? "({$prof->nivel_jerarquico})" : '' }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label fw-semibold">
            Materias que dicta <span class="text-danger">*</span>
          </label>
          <div id="materias-container" class="border rounded p-2 @error('materias') border-danger @enderror">
            @foreach($materias as $mat)
              <div class="form-check materia-item" data-sigla="{{ $mat->sigla }}">
                <input class="form-check-input" type="checkbox"
                       name="materias[]"
                       value="{{ $mat->sigla }}"
                       id="mat_{{ $mat->sigla }}"
                       {{ in_array($mat->sigla, $materiasActuales) ? 'checked' : '' }}>
                <label class="form-check-label" for="mat_{{ $mat->sigla }}">
                  <strong>{{ $mat->sigla }}</strong> — {{ $mat->nombre }}
                </label>
              </div>
            @endforeach
          </div>
          @error('materias')
            <div class="text-danger small mt-1">{{ $message }}</div>
          @enderror
          <small class="text-muted">Determina qué grupos puede recibir este docente</small>
        </div>

        <div class="col-md-3">
          <label class="form-label">Años de experiencia <span class="text-danger">*</span></label>
          <input type="number" name="anios_experiencia" class="form-control"
                 value="{{ old('anios_experiencia', $docente->anios_experiencia) }}" min="0" max="50" required>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label class="form-label">Certificado docente</label>
          @if($docente->certif_docente)
            <p class="text-muted small mb-1">
              <i class="bi bi-file-earmark-pdf me-1"></i>
              <a href="{{ asset('storage/' . $docente->certif_docente) }}" target="_blank">Ver archivo actual</a>
            </p>
          @endif
          <input type="file" name="certif_docente" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
          <small class="text-muted">Dejar vacío para mantener el actual</small>
        </div>
        <div class="col-md-6">
          <label class="form-label">Certificado profesional</label>
          @if($docente->certif_profesional)
            <p class="text-muted small mb-1">
              <i class="bi bi-file-earmark-pdf me-1"></i>
              <a href="{{ asset('storage/' . $docente->certif_profesional) }}" target="_blank">Ver archivo actual</a>
            </p>
          @endif
          <input type="file" name="certif_profesional" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
          <small class="text-muted">Dejar vacío para mantener el actual</small>
        </div>
      </div>

      <hr>
      <div class="d-flex gap-2 justify-content-end">
        <a href="{{ route('docentes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-cup-primary">
          <i class="bi bi-check-circle me-1"></i> Actualizar Docente
        </button>
      </div>
    </form>
  </div>
</div>

<script>
(function () {
  const materiasPermitidas = {
    'MAT': ['MAT', 'COM'],           // Matemáticas puede dictar Mat y Computación
    'FIS': ['FIS', 'MAT'],           // Física puede dictar Fis y Matemáticas
    'COM': ['COM'],                  // Computación solo Computación
    'ING': ['ING'],                  // Inglés solo Inglés
    'TEC': ['FIS', 'MAT', 'COM'],    // Telecomunicaciones puede dictar Fis, Mat y Comp
  };

  const selProfesion = document.getElementById('sel-profesion');
  if (!selProfesion) return;

  function filtrarMaterias() {
    const opt = selProfesion.options[selProfesion.selectedIndex];
    const sigla = opt ? opt.dataset.materia : '';
    const permitidas = sigla ? (materiasPermitidas[sigla] || null) : null;

    document.querySelectorAll('#materias-container .materia-item').forEach(function (item) {
      const itemSigla = item.dataset.sigla;
      const visible = !permitidas || permitidas.includes(itemSigla);
      item.style.display = visible ? '' : 'none';
      if (!visible) {
        const cb = item.querySelector('input[type=checkbox]');
        if (cb) cb.checked = false;
      }
    });
  }

  selProfesion.addEventListener('change', filtrarMaterias);
  filtrarMaterias(); // aplicar al cargar para respetar la profesión guardada
})();
</script>

@endsection
