@extends('layouts.base')
@section('titulo', 'Dashboard')

@section('contenido')
<div class="row g-4 mb-4">
    <div class="col-12">
        <h4><i class="bi bi-speedometer2 me-2"></i>Panel de Administración — CUP FICCT</h4>
        <p class="text-muted">
            @php $p = \App\Models\Periodo::where('activo', true)->first(); @endphp
            Periodo activo: <strong>{{ $p ? $p->fecha_ini_curso->format('Y') . ' — Gestión 1' : 'Sin periodo activo' }}</strong>
            @auth — Bienvenido, <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->rol?->nombre ?? 'Sin rol' }})@endauth
        </p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <i class="bi bi-people fs-1 text-primary"></i>
            <h3 class="mt-2">{{ \App\Models\Postulante::count() }}</h3>
            <p class="text-muted mb-2">Postulantes</p>
            <a href="{{ route('postulantes.index') }}" class="btn btn-sm btn-outline-primary">Ver todos</a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <i class="bi bi-person-workspace fs-1 text-success"></i>
            <h3 class="mt-2">{{ \App\Models\Docente::count() }}</h3>
            <p class="text-muted mb-2">Docentes</p>
            <a href="{{ route('docentes.index') }}" class="btn btn-sm btn-outline-success">Ver todos</a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <i class="bi bi-journal-bookmark fs-1 text-warning"></i>
            <h3 class="mt-2">{{ \App\Models\Carrera::count() }}</h3>
            <p class="text-muted mb-2">Carreras</p>
            <a href="{{ route('carreras.index') }}" class="btn btn-sm btn-outline-warning">Ver todas</a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <i class="bi bi-folder2-open fs-1 text-danger"></i>
            <h3 class="mt-2">{{ \App\Models\DocumentoPostulante::where('estado','pendiente')->count() }}</h3>
            <p class="text-muted mb-2">Docs pendientes</p>
            <a href="{{ route('documentos.index') }}" class="btn btn-sm btn-outline-danger">Revisar</a>
        </div>
    </div>
</div>

{{-- Accesos rápidos seguridad (solo admin) --}}
@auth
@if(Auth::user()->tienePermiso('usuarios.ver') || Auth::user()->tienePermiso('roles.ver'))
<div class="row g-4 mt-1">
    @if(Auth::user()->tienePermiso('usuarios.ver'))
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <i class="bi bi-people-fill fs-1 text-primary"></i>
            <h3 class="mt-2">{{ \App\Models\User::count() }}</h3>
            <p class="text-muted mb-2">Usuarios</p>
            <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-outline-primary">Gestionar</a>
        </div>
    </div>
    @endif
    @if(Auth::user()->tienePermiso('roles.ver'))
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <i class="bi bi-person-badge fs-1 text-secondary"></i>
            <h3 class="mt-2">{{ \App\Models\Rol::count() }}</h3>
            <p class="text-muted mb-2">Roles</p>
            <a href="{{ route('roles.index') }}" class="btn btn-sm btn-outline-secondary">Gestionar</a>
        </div>
    </div>
    @endif
    @if(Auth::user()->tienePermiso('bitacora.ver'))
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <i class="bi bi-journal-text fs-1 text-info"></i>
            <h3 class="mt-2">{{ \App\Models\Bitacora::whereDate('created_at', today())->count() }}</h3>
            <p class="text-muted mb-2">Eventos hoy</p>
            <a href="{{ route('bitacora.index') }}" class="btn btn-sm btn-outline-info">Ver bitácora</a>
        </div>
    </div>
    @endif
</div>
@endif
@endauth
@endsection