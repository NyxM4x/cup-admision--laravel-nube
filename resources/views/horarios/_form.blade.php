@php $h = $horario; @endphp

<div class="row g-3 mb-3">
  <div class="col-md-4">
    <label class="form-label">Código <span class="text-danger">*</span></label>
    <input type="text" name="codigo" class="form-control" maxlength="20" required
           value="{{ old('codigo', $h->codigo ?? '') }}" placeholder="ej: M1">
  </div>
  <div class="col-md-4">
    <label class="form-label">Turno <span class="text-danger">*</span></label>
    <select name="turno" class="form-select" required>
      @foreach(['Mañana', 'Tarde', 'Noche'] as $t)
        <option value="{{ $t }}" {{ old('turno', $h->turno ?? '') === $t ? 'selected' : '' }}>{{ $t }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label">Días <span class="text-danger">*</span></label>
    <input type="text" name="dias" class="form-control" maxlength="50" required list="dias-sugeridos"
           value="{{ old('dias', $h->dias ?? '') }}" placeholder="ej: Lunes,Miércoles,Viernes">
    <datalist id="dias-sugeridos">
      <option value="Lunes,Miércoles,Viernes">
      <option value="Martes,Jueves">
      <option value="Sábado">
    </datalist>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-4">
    <label class="form-label">Hora inicio <span class="text-danger">*</span></label>
    <input type="time" name="hora_inicio" class="form-control" required
           value="{{ old('hora_inicio', isset($h) ? substr($h->hora_inicio, 0, 5) : '') }}">
  </div>
  <div class="col-md-4">
    <label class="form-label">Hora fin <span class="text-danger">*</span></label>
    <input type="time" name="hora_fin" class="form-control" required
           value="{{ old('hora_fin', isset($h) ? substr($h->hora_fin, 0, 5) : '') }}">
  </div>
</div>

<div class="mb-2">
  <label class="form-label">Descripción</label>
  <input type="text" name="descripcion" class="form-control" maxlength="200"
         value="{{ old('descripcion', $h->descripcion ?? '') }}" placeholder="ej: Mañana temprana - LMV">
</div>
