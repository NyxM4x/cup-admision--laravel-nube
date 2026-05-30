@props([
  'q' => '',
  'estado' => 'activos',
  'placeholder' => 'Buscar por nombre, CI...',
  'mostrarEstado' => true,
])

<form method="GET" class="row g-2 align-items-center mb-3">
  <div class="{{ $mostrarEstado ? 'col-md-7' : 'col-md-10' }}">
    <div class="input-group">
      <span class="input-group-text bg-white">
        <i class="bi bi-search text-muted"></i>
      </span>
      <input type="text" name="q" value="{{ $q }}"
             class="form-control"
             placeholder="{{ $placeholder }}">
    </div>
  </div>

  @if($mostrarEstado)
  <div class="col-md-3">
    <select name="estado" class="form-select">
      <option value="activos"   {{ $estado === 'activos'   ? 'selected' : '' }}>Activos</option>
      <option value="inactivos" {{ $estado === 'inactivos' ? 'selected' : '' }}>Inactivos</option>
      <option value="todos"     {{ $estado === 'todos'     ? 'selected' : '' }}>Todos</option>
    </select>
  </div>
  @endif

  <div class="col-md-2">
    <button type="submit" class="btn btn-cup-primary w-100">
      <i class="bi bi-funnel me-1"></i> Filtrar
    </button>
  </div>
</form>
