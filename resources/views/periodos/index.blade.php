
@extends('layouts.base')
@section('titulo', 'Periodos')
@section('contenido')
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Periodos Académicos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Periodos Académicos</h2>
        <a href="{{ route('periodos.create') }}" class="btn btn-primary">+ Nuevo Periodo</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped bg-white">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Inicio Inscripción</th>
                <th>Fin Inscripción</th>
                <th>Inicio Curso</th>
                <th>Fin Curso</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($periodos as $periodo)
            <tr>
                <td>{{ $periodo->id }}</td>
                <td>{{ $periodo->fecha_ini_inscripcion->format('d/m/Y') }}</td>
                <td>{{ $periodo->fecha_fin_inscripcion->format('d/m/Y') }}</td>
                <td>{{ $periodo->fecha_ini_curso->format('d/m/Y') }}</td>
                <td>{{ $periodo->fecha_fin_curso->format('d/m/Y') }}</td>
                <td>
                    @if($periodo->activo)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-secondary">Inactivo</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('periodos.edit', $periodo) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('periodos.destroy', $periodo) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('¿Eliminar este periodo?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted">No hay periodos registrados.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
