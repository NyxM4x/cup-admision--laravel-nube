<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Requisito</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4" style="max-width:600px">
    <h2>Editar Requisito</h2>
    <a href="{{ route('requisitos.index') }}" class="btn btn-secondary mb-3">← Volver</a>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('requisitos.update', $requisito) }}" method="POST" class="card p-4 bg-white">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Nombre del Requisito</label>
            <input type="text" name="nombre" class="form-control"
                   value="{{ $requisito->nombre }}" required maxlength="150">
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="2">{{ $requisito->descripcion }}</textarea>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="obligatorio" value="1" class="form-check-input" id="obligatorio"
       {{ old('obligatorio', $requisito->obligatorio) ? 'checked' : '' }}>
            <label class="form-check-label" for="obligatorio"><strong>Es obligatorio</strong></label>
        </div>
        <div class="mb-3">
            <label class="form-label">Formatos aceptados</label>
            <select name="formato_aceptado" class="form-select" required>
                <option value="PDF,JPG,PNG" {{ $requisito->formato_aceptado == 'PDF,JPG,PNG' ? 'selected' : '' }}>PDF, JPG, PNG</option>
                <option value="PDF"         {{ $requisito->formato_aceptado == 'PDF'         ? 'selected' : '' }}>Solo PDF</option>
                <option value="JPG,PNG"     {{ $requisito->formato_aceptado == 'JPG,PNG'     ? 'selected' : '' }}>Solo imágenes (JPG, PNG)</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Tamaño máximo del archivo</label>
            <select name="tamanio_max_kb" class="form-select" required>
                <option value="1024"  {{ $requisito->tamanio_max_kb == 1024  ? 'selected' : '' }}>1 MB</option>
                <option value="2048"  {{ $requisito->tamanio_max_kb == 2048  ? 'selected' : '' }}>2 MB</option>
                <option value="5120"  {{ $requisito->tamanio_max_kb == 5120  ? 'selected' : '' }}>5 MB</option>
                <option value="10240" {{ $requisito->tamanio_max_kb == 10240 ? 'selected' : '' }}>10 MB</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success w-100">Actualizar Requisito</button>
    </form>
</div>
</body>
</html>