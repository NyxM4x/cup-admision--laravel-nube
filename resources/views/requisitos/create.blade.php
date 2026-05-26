<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Requisito</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4" style="max-width:600px">
    <h2>Nuevo Requisito de Inscripción</h2>
    <a href="{{ route('requisitos.index') }}" class="btn btn-secondary mb-3">← Volver</a>

    @if(!$periodoActivo)
        <div class="alert alert-danger">
            ❌ No hay periodo activo. <a href="{{ route('periodos.create') }}">Crear periodo primero</a>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('requisitos.store') }}" method="POST" class="card p-4 bg-white">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nombre del Requisito</label>
            <input type="text" name="nombre" class="form-control"
                   value="{{ old('nombre') }}" required maxlength="150"
                   placeholder="ej: Fotocopia de CI">
        </div>
        <div class="mb-3">
            <label class="form-label">Descripción <small class="text-muted">(opcional)</small></label>
            <textarea name="descripcion" class="form-control" rows="2"
                      placeholder="Instrucciones adicionales para el postulante">{{ old('descripcion') }}</textarea>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="obligatorio" value="1" class="form-check-input" id="obligatorio"
       {{ old('obligatorio', true) ? 'checked' : '' }}>
            <label class="form-check-label" for="obligatorio">
                <strong>Es obligatorio</strong>
            </label>
        </div>
        <div class="mb-3">
            <label class="form-label">Formatos aceptados</label>
            <select name="formato_aceptado" class="form-select" required>
                <option value="PDF,JPG,PNG" {{ old('formato_aceptado') == 'PDF,JPG,PNG' ? 'selected' : '' }}>PDF, JPG, PNG</option>
                <option value="PDF"         {{ old('formato_aceptado') == 'PDF'         ? 'selected' : '' }}>Solo PDF</option>
                <option value="JPG,PNG"     {{ old('formato_aceptado') == 'JPG,PNG'     ? 'selected' : '' }}>Solo imágenes (JPG, PNG)</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Tamaño máximo del archivo</label>
            <select name="tamanio_max_kb" class="form-select" required>
                <option value="1024"  {{ old('tamanio_max_kb') == '1024'  ? 'selected' : '' }}>1 MB</option>
                <option value="2048"  {{ old('tamanio_max_kb', '2048') == '2048' ? 'selected' : '' }}>2 MB</option>
                <option value="5120"  {{ old('tamanio_max_kb') == '5120'  ? 'selected' : '' }}>5 MB</option>
                <option value="10240" {{ old('tamanio_max_kb') == '10240' ? 'selected' : '' }}>10 MB</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100" {{ !$periodoActivo ? 'disabled' : '' }}>
            Guardar Requisito
        </button>
    </form>
</div>
</body>
</html>