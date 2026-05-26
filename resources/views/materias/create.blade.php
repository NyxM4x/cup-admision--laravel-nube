<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Materia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4" style="max-width:600px">
    <h2>Nueva Materia</h2>
    <a href="{{ route('materias.index') }}" class="btn btn-secondary mb-3">← Volver</a>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('materias.store') }}" method="POST" class="card p-4 bg-white">
        @csrf
        <div class="mb-3">
            <label class="form-label">Sigla <small class="text-muted">(ej: MAT, FIS)</small></label>
            <input type="text" name="sigla" class="form-control text-uppercase"
                   value="{{ old('sigla') }}" required maxlength="20">
        </div>
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control"
                   value="{{ old('nombre') }}" required maxlength="100">
        </div>
        <div class="mb-3">
            <label class="form-label">Días de dictado</label>
            <select name="dias" class="form-select" required>
                <option value="">-- Seleccionar --</option>
                <option value="LMV" {{ old('dias') == 'LMV' ? 'selected' : '' }}>LMV (Lunes, Miércoles, Viernes)</option>
                <option value="MJ" {{ old('dias') == 'MJ' ? 'selected' : '' }}>MJ (Martes, Jueves)</option>
            </select>
        </div>
        <hr>
        <h6 class="text-primary">📊 Pesos de exámenes <small class="text-muted">(deben sumar 100%)</small></h6>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Examen 1 (%)</label>
                <input type="number" name="peso_examen1" class="form-control"
                       value="{{ old('peso_examen1', 30) }}" required min="1" max="98" step="0.01"
                       oninput="calcularSuma()">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Examen 2 (%)</label>
                <input type="number" name="peso_examen2" class="form-control"
                       value="{{ old('peso_examen2', 30) }}" required min="1" max="98" step="0.01"
                       oninput="calcularSuma()">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Examen 3 (%)</label>
                <input type="number" name="peso_examen3" class="form-control"
                       value="{{ old('peso_examen3', 40) }}" required min="1" max="98" step="0.01"
                       oninput="calcularSuma()">
            </div>
        </div>
        <div id="suma-pesos" class="alert alert-info py-2">
            Total: <strong id="total-pesos">100</strong>% 
            <span id="suma-ok">✅ Correcto</span>
        </div>
        <button type="submit" class="btn btn-primary w-100">Guardar Materia</button>
    </form>
</div>
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
        div.className = 'alert alert-success py-2';
        ok.textContent = '✅ Correcto';
    } else {
        div.className = 'alert alert-danger py-2';
        ok.textContent = '❌ Debe sumar 100%';
    }
}
</script>
</body>
</html>