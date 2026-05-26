<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Docente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4" style="max-width:700px">
    <h2>👨‍🏫 Nuevo Docente</h2>
    <a href="{{ route('docentes.index') }}" class="btn btn-secondary mb-3">← Volver</a>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('docentes.store') }}" method="POST" 
          enctype="multipart/form-data" class="card p-4 bg-white">
        @csrf

        <h5 class="mb-3 text-primary">📋 Datos Personales</h5>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">CI <span class="text-danger">*</span></label>
                <input type="text" name="ci" class="form-control"
                       value="{{ old('ci') }}" required maxlength="20"
                       placeholder="ej: 1234567">
            </div>
            <div class="col-md-8">
                <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                <input type="text" name="nombre" class="form-control"
                       value="{{ old('nombre') }}" required maxlength="200"
                       placeholder="ej: Juan Carlos Pérez López">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control"
                       value="{{ old('fecha_nacimiento') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Sexo</label>
                <select name="sexo" class="form-select">
                    <option value="">— Seleccionar —</option>
                    <option value="M" {{ old('sexo') == 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('sexo') == 'F' ? 'selected' : '' }}>Femenino</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-control"
                   value="{{ old('direccion') }}" maxlength="255"
                   placeholder="ej: Av. Cañoto #123, Santa Cruz">
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control"
                       value="{{ old('telefono') }}" maxlength="20"
                       placeholder="ej: 70000000">
            </div>
            <div class="col-md-6">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="correo" class="form-control"
                       value="{{ old('correo') }}" maxlength="150"
                       placeholder="ej: docente@uagrm.edu.bo">
            </div>
        </div>

        <hr>
        <h5 class="mb-3 text-primary">🎓 Datos Académicos</h5>

        <div class="row mb-3">
            <div class="col-md-8">
                <label class="form-label">Profesión</label>
                <select name="profesion_id" class="form-select">
                    <option value="">— Sin especificar —</option>
                    @foreach($profesiones as $prof)
                        <option value="{{ $prof->id }}"
                            {{ old('profesion_id') == $prof->id ? 'selected' : '' }}>
                            {{ $prof->nombre }}
                            {{ $prof->nivel_jerarquico ? "({$prof->nivel_jerarquico})" : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Años de experiencia <span class="text-danger">*</span></label>
                <input type="number" name="anios_experiencia" class="form-control"
                       value="{{ old('anios_experiencia', 0) }}" min="0" max="50" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Certificado docente 
                    <small class="text-muted">(PDF/JPG/PNG, máx 5MB)</small>
                </label>
                <input type="file" name="certif_docente" class="form-control"
                       accept=".pdf,.jpg,.jpeg,.png">
            </div>
            <div class="col-md-6">
                <label class="form-label">Certificado profesional
                    <small class="text-muted">(PDF/JPG/PNG, máx 5MB)</small>
                </label>
                <input type="file" name="certif_profesional" class="form-control"
                       accept=".pdf,.jpg,.jpeg,.png">
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            💾 Guardar Docente
        </button>
    </form>
</div>
</body>
</html>