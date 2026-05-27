@extends('layouts.base')
@section('titulo', 'Periodos')
@section('contenido')
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Requisitos de Inscripción</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Requisitos de Inscripción</h2>
        <a href="{{ route('requisitos.create') }}" class="btn btn-primary">+ Nuevo Requisito</a>
    </div>

    @if(!$periodoActivo)
        <div class="alert alert-warning">
            ⚠️ No hay periodo activo. <a href="{{ route('periodos.create') }}" class="alert-link">Crear periodo</a>
        </div>
    @else
        <div class="alert alert-info">
            📅 Periodo activo: <strong>{{ $periodoActivo->fecha_ini_inscripcion->format('d/m/Y') }} — {{ $periodoActivo->fecha_fin_curso->format('d/m/Y') }}</strong>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped bg-white">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Obligatorio</th>
                <th>Formatos</th>
                <th>Tamaño máx.</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requisitos as $req)
            <tr>
                <td>{{ $req->id }}</td>
                <td><strong>{{ $req->nombre }}</strong></td>
                <td>{{ $req->descripcion ?? '—' }}</td>
                <td>
                    @if($req->obligatorio)
                        <span class="badge bg-danger">Obligatorio</span>
                    @else
                        <span class="badge bg-secondary">Opcional</span>
                    @endif
                </td>
                <td><code>{{ $req->formato_aceptado }}</code></td>
                <td>{{ number_format($req->tamanio_max_kb / 1024, 1) }} MB</td>
                <td>
                    @if($req->activo)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-secondary">Inactivo</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('requisitos.edit', $req) }}" class="btn btn-sm btn-warning">Editar</a>
                    @if($req->activo)
                        <form action="{{ route('requisitos.destroy', $req) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Desactivar este requisito?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Desactivar</button>
                        </form>
                    @else
                        <form action="{{ route('requisitos.reactivar', $req) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-success">Reactivar</button>
                        </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-muted">No hay requisitos registrados para este periodo.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection