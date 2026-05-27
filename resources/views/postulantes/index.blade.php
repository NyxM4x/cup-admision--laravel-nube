@extends('layouts.base')
@section('titulo', 'Periodos')
@section('contenido')
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Postulantes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>🎓 Postulantes Inscritos</h2>
        <a href="{{ route('postulantes.create') }}" class="btn btn-primary">+ Registrar Postulante</a>
    </div>

    @if(!$periodoActivo)
        <div class="alert alert-warning">⚠️ No hay periodo activo. Los postulantes no pueden inscribirse.</div>
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
                        <th>Nombre</th>
                        <th>Colegio</th>
                        <th>1ra Carrera</th>
                        <th>2da Carrera</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($postulantes as $postulante)
                    @php
                        $inscripcion = $postulante->inscripciones->first();
                        $c1 = $inscripcion?->postulacionCarreras->where('prioridad',1)->first();
                        $c2 = $inscripcion?->postulacionCarreras->where('prioridad',2)->first();
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $postulante->persona->ci }}</td>
                        <td>{{ $postulante->persona->nombre }}</td>
                        <td>{{ $postulante->colegio }}</td>
                        <td>{{ $c1?->carrera->nombre ?? '—' }}</td>
                        <td>{{ $c2?->carrera->nombre ?? '—' }}</td>
                        <td>
                            @php
                                $badges = [
                                    'pendiente' => 'warning',
                                    'inscrito'  => 'primary',
                                    'aprobado'  => 'success',
                                    'reprobado' => 'danger',
                                ];
                            @endphp
                            <span class="badge bg-{{ $badges[$postulante->estado] ?? 'secondary' }}">
                                {{ ucfirst($postulante->estado) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('postulantes.show', $postulante) }}"
                               class="btn btn-sm btn-info text-white">Ver</a>
                            <a href="{{ route('postulantes.edit', $postulante) }}"
                               class="btn btn-sm btn-warning">Editar</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No hay postulantes registrados aún.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection