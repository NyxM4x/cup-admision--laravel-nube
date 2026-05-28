<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CUP-FICCT | @yield('titulo', 'Sistema de Admisión')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }

        /* Navbar */
        .navbar-cup { background-color: #1a3a5c; }
        .navbar-cup .navbar-brand { color: #fff; font-weight: 700; font-size: 1.1rem; }
        .navbar-cup .nav-link { color: rgba(255,255,255,.85) !important; font-size: 0.9rem; }
        .navbar-cup .nav-link:hover { color: #fff !important; }
        .navbar-cup .nav-link.active { color: #fff !important; font-weight: 600;
            border-bottom: 2px solid #4fc3f7; }
        .navbar-cup .dropdown-menu { border: none; box-shadow: 0 4px 15px rgba(0,0,0,.15); }

        /* Sidebar indicator */
        .nav-section { font-size: .7rem; color: rgba(255,255,255,.45);
            text-transform: uppercase; letter-spacing: .08em;
            padding: .25rem 1rem; margin-top: .25rem; }
        .dropdown-menu .nav-section { color: #6c757d; }

        /* Content */
        .page-content { padding: 1.5rem 0; }
        .page-header { background: #fff; border-bottom: 1px solid #dee2e6;
            padding: .75rem 0; margin-bottom: 1.5rem; }
        .page-header h4 { margin: 0; font-weight: 600; color: #1a3a5c; }
    </style>
    @stack('styles')
</head>
<body>

{{-- ═══════════════════════════════════════════ --}}
{{-- NAVBAR PRINCIPAL                            --}}
{{-- ═══════════════════════════════════════════ --}}
<nav class="navbar navbar-expand-lg navbar-cup sticky-top">
    <div class="container-fluid px-4">

        {{-- Logo / Brand --}}
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            <i class="bi bi-mortarboard-fill fs-5"></i>
            <span>CUP <span class="fw-light">FICCT</span></span>
        </a>

        {{-- Toggler mobile --}}
        <button class="navbar-toggler border-0" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon" style="filter:invert(1)"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">

            {{-- Links principales --}}
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>

                {{-- Gestión Académica --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle
                        {{ request()->routeIs('periodos.*','carreras.*','materias.*') ? 'active' : '' }}"
                       href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-building me-1"></i>Gestión Académica
                    </a>
                    <ul class="dropdown-menu">
                        <li><span class="nav-section">Administración</span></li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('periodos.*') ? 'fw-bold' : '' }}"
                               href="{{ route('periodos.index') }}">
                                <i class="bi bi-calendar3 me-2 text-primary"></i>Periodos
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('carreras.*') ? 'fw-bold' : '' }}"
                               href="{{ route('carreras.index') }}">
                                <i class="bi bi-journal-bookmark me-2 text-success"></i>Carreras
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('materias.*') ? 'fw-bold' : '' }}"
                               href="{{ route('materias.index') }}">
                                <i class="bi bi-book me-2 text-warning"></i>Materias
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Inscripciones --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle
                        {{ request()->routeIs('requisitos.*','postulantes.*','documentos.*') ? 'active' : '' }}"
                       href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-plus me-1"></i>Inscripciones
                    </a>
                    <ul class="dropdown-menu">
                        <li><span class="nav-section">Proceso de admisión</span></li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('requisitos.*') ? 'fw-bold' : '' }}"
                               href="{{ route('requisitos.index') }}">
                                <i class="bi bi-card-checklist me-2 text-danger"></i>Requisitos
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('postulantes.*') ? 'fw-bold' : '' }}"
                               href="{{ route('postulantes.index') }}">
                                <i class="bi bi-people me-2 text-primary"></i>Postulantes
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('documentos.*') ? 'fw-bold' : '' }}"
                               href="{{ route('documentos.index') }}">
                                <i class="bi bi-folder2-open me-2 text-warning"></i>Documentación
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Docentes --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('docentes.*') ? 'active' : '' }}"
                       href="{{ route('docentes.index') }}">
                        <i class="bi bi-person-workspace me-1"></i>Docentes
                    </a>
                </li>

                {{-- Gestión Global (Aulas) --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('aulas.*') ? 'active' : '' }}"
                       href="{{ route('aulas.index') }}">
                        <i class="bi bi-door-open me-1"></i>Aulas
                    </a>
                </li>

                {{-- Seguridad --}}
                @auth
                @if(Auth::user()->tienePermiso('usuarios.ver') || Auth::user()->tienePermiso('roles.ver') || Auth::user()->tienePermiso('permisos.gestionar'))
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle
                        {{ request()->routeIs('usuarios.*','roles.*','permisos.*') ? 'active' : '' }}"
                       href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-shield-lock me-1"></i>Seguridad
                    </a>
                    <ul class="dropdown-menu">
                        <li><span class="nav-section">Administración del sistema</span></li>
                        @if(Auth::user()->tienePermiso('usuarios.ver'))
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('usuarios.*') ? 'fw-bold' : '' }}"
                               href="{{ route('usuarios.index') }}">
                                <i class="bi bi-people-fill me-2 text-primary"></i>Usuarios
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->tienePermiso('roles.ver'))
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('roles.*') ? 'fw-bold' : '' }}"
                               href="{{ route('roles.index') }}">
                                <i class="bi bi-person-badge me-2 text-success"></i>Roles
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->tienePermiso('permisos.gestionar'))
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('permisos.*') ? 'fw-bold' : '' }}"
                               href="{{ route('permisos.index') }}">
                                <i class="bi bi-key me-2 text-warning"></i>Permisos
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                {{-- Bitácora --}}
                @if(Auth::user()->tienePermiso('bitacora.ver'))
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('bitacora.*') ? 'active' : '' }}"
                       href="{{ route('bitacora.index') }}">
                        <i class="bi bi-journal-text me-1"></i>Bitácora
                    </a>
                </li>
                @endif
                @endauth

            </ul>

            {{-- Usuario + Cerrar sesión --}}
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                       href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-5"></i>
                        <span class="d-none d-lg-inline">
                            {{ Auth::check() ? Auth::user()->name : 'Administrador' }}
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @auth
                        <li>
                            <span class="dropdown-item text-muted small disabled">
                                <i class="bi bi-envelope me-2"></i>{{ Auth::user()->email }}
                            </span>
                        </li>
                        <li>
                            <span class="dropdown-item text-muted small disabled">
                                <i class="bi bi-person-badge me-2"></i>{{ Auth::user()->rol?->nombre ?? 'Sin rol' }}
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-gear me-2"></i>Perfil
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                                </button>
                            </form>
                        </li>
                        @endauth
                    </ul>
                </li>
            </ul>

        </div>
    </div>
</nav>

{{-- ═══════════════════════════════════════════ --}}
{{-- CONTENIDO DE LA PÁGINA                      --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="page-content">
    <div class="container-fluid px-4">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('contenido')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>