<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Postulante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4" style="max-width:700px">
    <h2>✏️ Editar Postulante</h2>
    <a href="{{ route('postulantes.index') }}" class="btn btn-secondary mb-3">← Volver</a>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('postulantes.update', $postulante) }}" method="POST" class="card p-4 bg-white">
        @csrf
        @method('PUT')

        <h5 class="text-primary mb-3">📋 Datos Personales</h5>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">CI <span class="text-danger">*</span></label>
                <input type="text" name="ci" class="form-control"
                       value="{{ old('ci', $postulante->persona->ci) }}" required maxlength="20">
            </div>
            <div class="col-md-8">
                <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                <input type="text" name="nombre" class="form-control"
                       value="{{ old('nombre', $postulante->persona->nombre) }}" required maxlength="200">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control"
                       value="{{ old('fecha_nacimiento', optional($postulante->persona->fecha_nacimiento)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Sexo</label>
                <select name="sexo" class="form-select">
                    <option value="">— Seleccionar —</option>
                    <option value="M" {{ old('sexo', $postulante->persona->sexo) == 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('sexo', $postulante->persona->sexo) == 'F' ? 'selected' : '' }}>Femenino</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-control"
                   value="{{ old('direccion', $postulante->persona->direccion) }}" maxlength="255">
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control"
                       value="{{ old('telefono', $postulante->persona->telefono) }}" maxlength="20">
            </div>
            <div class="col-md-6">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="correo" class="form-control"
                       value="{{ old('correo', $postulante->persona->correo) }}" maxlength="150">
            </div>
        </div>

        <hr>
        <h5 class="text-primary mb-3">🏫 Datos del Postulante</h5>

        <div class="mb-3">
            <label class="form-label">Colegio de procedencia <span class="text-danger">*</span></label>
            <input type="text" name="colegio" class="form-control"
                   value="{{ old('colegio', $postulante->colegio) }}" required maxlength="200">
        </div>

        <hr>
        <h5 class="text-primary mb-3">📚 Carreras a Postular</h5>

        <div class="mb-3">
            <label class="form-label">1ra opción <span class="text-danger">*</span></label>
            <select name="carrera_1" class="form-select" required>
                <option value="">— Seleccionar carrera —</option>
                @foreach($carreras as $carrera)
                    <option value="{{ $carrera->id }}"
                        {{ old('carrera_1', $carrera1?->carrera_id) == $carrera->id ? 'selected' : '' }}>
                        {{ $carrera->codigo }} — {{ $carrera->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">2da opción <small class="text-muted">(opcional)</small></label>
            <select name="carrera_2" class="form-select">
                <option value="">— Sin segunda opción —</option>
                @foreach($carreras as $carrera)
                    <option value="{{ $carrera->id }}"
                        {{ old('carrera_2', $carrera2?->carrera_id) == $carrera->id ? 'selected' : '' }}>
                        {{ $carrera->codigo }} — {{ $carrera->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-warning w-100">
            💾 Actualizar Postulante
        </button>
    </form>
</div>
</body>
</html>