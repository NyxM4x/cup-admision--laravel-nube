@extends('layouts.base')
@section('titulo', 'Periodos')
@section('contenido')
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documentación Postulantes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>📁 Documentación de Postulantes</h2>
    </div>

    @if(!$periodoActivo)
        <div class="alert alert-warning">⚠️ No hay periodo activo.</div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>CI</th>
                        <th>Postulante</th>
                        <th>1ra Carrera</th>
                        <th>Docs Subidos</th>
                        <th>Aprobados</th>
                        <th>Rechazados</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inscripciones as $inscripcion)
                    @php
                        $c1 = $inscripcion->postulacionCarreras->where('prioridad',1)->first();
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $inscripcion->postulante->persona->ci }}</td>
                        <td>{{ $inscripcion->postulante->persona->nombre }}</td>
                        <td>{{ $c1?->carrera->nombre ?? '—' }}</td>
                        <td>{{ $inscripcion->total_subidos }} / {{ $inscripcion->total_requisitos }}</td>
                        <td>
                            <span class="badge bg-success">{{ $inscripcion->aprobados }}</span>
                        </td>
                        <td>
                            <span class="badge bg-danger">{{ $inscripcion->rechazados }}</span>
                        </td>
                        <td>
                            @if($inscripcion->completo)
                                <span class="badge bg-success">✅ Completo</span>
                            @elseif($inscripcion->total_subidos == 0)
                                <span class="badge bg-secondary">Sin docs</span>
                            @else
                                <span class="badge bg-warning text-dark">En revisión</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('documentos.show', $inscripcion) }}"
                               class="btn btn-sm btn-primary">Gestionar</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            No hay postulantes inscritos en el periodo activo.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection