<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CUP-FICCT | @yield('titulo', 'Sistema de Admisión')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Paleta UAGRM y componentes -->
    <style>
      :root {
        --cup-primary:        #0d2c5e;
        --cup-primary-light:  #1e5fa8;
        --cup-accent:         #2c7be5;
        --cup-success:        #198754;
        --cup-warning:        #f59e0b;
        --cup-danger:         #dc2626;
        --cup-info:           #0dcaf0;
        --cup-bg:             #f8f9fb;
        --cup-text:           #1f2937;
        --cup-muted:          #6b7280;
        --cup-border:         #e5e7eb;
      }

      body {
        background-color: var(--cup-bg);
        color: var(--cup-text);
        font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, sans-serif;
      }

      /* Header navbar institucional */
      .navbar-cup {
        background-color: var(--cup-primary) !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
      }
      .navbar-cup .navbar-brand,
      .navbar-cup .nav-link {
        color: #ffffff !important;
      }
      .navbar-cup .nav-link:hover,
      .navbar-cup .nav-link.active {
        color: #ffd54f !important;
      }
      .navbar-cup .nav-link.active {
        border-bottom: 2px solid #ffd54f;
      }

      /* Logo institucional placeholder */
      .cup-brand {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 700;
        color: #fff;
        text-decoration: none;
        letter-spacing: 0.3px;
      }
      .cup-brand-mark {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: linear-gradient(135deg, #ffd54f 0%, #ffb300 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--cup-primary);
        font-weight: 800;
        font-size: 14px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
      }
      .cup-brand-text {
        line-height: 1.1;
      }
      .cup-brand-text .main {
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.5px;
      }
      .cup-brand-text .sub {
        font-size: 10px;
        font-weight: 400;
        opacity: 0.85;
        letter-spacing: 1.2px;
        text-transform: uppercase;
      }

      /* TARJETAS KPI con borde lateral + sombra suave */
      .kpi-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid var(--cup-border);
        border-left: 4px solid var(--cup-accent);
        padding: 1.25rem 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: all 0.2s ease;
        height: 100%;
      }
      .kpi-card:hover {
        box-shadow: 0 8px 24px rgba(13,44,94,0.08);
        transform: translateY(-2px);
      }
      .kpi-card.kpi-primary  { border-left-color: var(--cup-primary-light); }
      .kpi-card.kpi-success  { border-left-color: var(--cup-success); }
      .kpi-card.kpi-warning  { border-left-color: var(--cup-warning); }
      .kpi-card.kpi-danger   { border-left-color: var(--cup-danger); }
      .kpi-card.kpi-info     { border-left-color: var(--cup-info); }
      .kpi-card.kpi-accent   { border-left-color: var(--cup-accent); }

      .kpi-card .kpi-icon {
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: rgba(44,123,229,0.10);
        color: var(--cup-accent);
      }
      .kpi-card.kpi-primary  .kpi-icon { background: rgba(30,95,168,0.10); color: var(--cup-primary-light); }
      .kpi-card.kpi-success  .kpi-icon { background: rgba(25,135,84,0.10); color: var(--cup-success); }
      .kpi-card.kpi-warning  .kpi-icon { background: rgba(245,158,11,0.10); color: var(--cup-warning); }
      .kpi-card.kpi-danger   .kpi-icon { background: rgba(220,38,38,0.10); color: var(--cup-danger); }
      .kpi-card.kpi-info     .kpi-icon { background: rgba(13,202,240,0.10); color: var(--cup-info); }

      .kpi-card .kpi-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--cup-text);
        line-height: 1;
        margin-bottom: 0.25rem;
      }
      .kpi-card .kpi-label {
        font-size: 0.85rem;
        color: var(--cup-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 500;
      }

      /* Tarjetas / paneles */
      .panel-cup {
        background: #fff;
        border-radius: 12px;
        border: 1px solid var(--cup-border);
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
      }
      .panel-cup .panel-cup-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--cup-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      .panel-cup .panel-cup-body {
        padding: 1.5rem;
      }

      /* Botones de acción pequeños y elegantes */
      .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid transparent;
        font-size: 0.9rem;
        transition: all 0.15s ease;
      }
      .btn-action + .btn-action {
        margin-left: 0.35rem;
      }
      .btn-action-edit {
        background: rgba(30,95,168,0.10);
        color: var(--cup-primary-light);
        border-color: rgba(30,95,168,0.20);
      }
      .btn-action-edit:hover {
        background: var(--cup-primary-light);
        color: #fff;
      }
      .btn-action-danger {
        background: rgba(220,38,38,0.08);
        color: var(--cup-danger);
        border-color: rgba(220,38,38,0.20);
      }
      .btn-action-danger:hover {
        background: var(--cup-danger);
        color: #fff;
      }
      .btn-action-success {
        background: rgba(25,135,84,0.10);
        color: var(--cup-success);
        border-color: rgba(25,135,84,0.20);
      }
      .btn-action-success:hover {
        background: var(--cup-success);
        color: #fff;
      }
      .btn-action-view {
        background: rgba(44,123,229,0.10);
        color: var(--cup-accent);
        border-color: rgba(44,123,229,0.20);
      }
      .btn-action-view:hover {
        background: var(--cup-accent);
        color: #fff;
      }

      /* Botones primarios CUP */
      .btn-cup-primary {
        background-color: var(--cup-primary-light);
        border-color: var(--cup-primary-light);
        color: #fff;
        font-weight: 500;
      }
      .btn-cup-primary:hover {
        background-color: var(--cup-primary);
        border-color: var(--cup-primary);
        color: #fff;
      }

      /* Badges */
      .badge-cup {
        font-size: 0.72rem;
        font-weight: 600;
        padding: 0.35em 0.7em;
        border-radius: 6px;
        letter-spacing: 0.3px;
      }
      .badge-activo {
        background: rgba(25,135,84,0.12);
        color: var(--cup-success);
        border: 1px solid rgba(25,135,84,0.25);
      }
      .badge-inactivo {
        background: rgba(220,38,38,0.10);
        color: var(--cup-danger);
        border: 1px solid rgba(220,38,38,0.20);
      }
      .badge-modulo {
        background: rgba(30,95,168,0.10);
        color: var(--cup-primary-light);
        border: 1px solid rgba(30,95,168,0.20);
      }
      .badge-warning-cup {
        background: rgba(245,158,11,0.12);
        color: #b45309;
        border: 1px solid rgba(245,158,11,0.30);
      }

      /* Tablas mejoradas */
      .table-cup {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
      }
      .table-cup thead {
        background: #f3f4f6;
      }
      .table-cup thead th {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--cup-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid var(--cup-border);
        padding: 0.85rem 1rem;
      }
      .table-cup tbody td {
        padding: 0.85rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f3f4f6;
        font-size: 0.9rem;
      }
      .table-cup tbody tr:hover {
        background: #f9fafb;
      }
      .table-cup tbody tr:last-child td {
        border-bottom: none;
      }

      /* Encabezados de página */
      .page-header {
        margin-bottom: 1.5rem;
      }
      .page-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--cup-primary);
        margin-bottom: 0.25rem;
      }
      .page-header .page-subtitle {
        color: var(--cup-muted);
        font-size: 0.95rem;
      }

      /* Alerts más sutiles */
      .alert-cup-success {
        background: rgba(25,135,84,0.08);
        color: #0f5132;
        border: 1px solid rgba(25,135,84,0.25);
        border-radius: 8px;
      }
      .alert-cup-danger {
        background: rgba(220,38,38,0.06);
        color: #842029;
        border: 1px solid rgba(220,38,38,0.20);
        border-radius: 8px;
      }
      .alert-cup-warning {
        background: rgba(245,158,11,0.08);
        color: #92400e;
        border: 1px solid rgba(245,158,11,0.25);
        border-radius: 8px;
      }

      /* ── Preservado del layout original ── */
      .navbar-cup .nav-link { font-size: 0.9rem; }
      .navbar-cup .dropdown-menu { border: none; box-shadow: 0 4px 15px rgba(0,0,0,.15); }
      .nav-section { font-size: .7rem; color: rgba(255,255,255,.45);
          text-transform: uppercase; letter-spacing: .08em;
          padding: .25rem 1rem; margin-top: .25rem; }
      .dropdown-menu .nav-section { color: #6c757d; }
      .page-content { padding: 1.5rem 0; }
    </style>
    @stack('styles')
</head>
<body>

{{-- ═══════════════════════════════════════════ --}}
{{-- NAVBAR PRINCIPAL                            --}}
{{-- ═══════════════════════════════════════════ --}}
<nav class="navbar navbar-expand-lg navbar-cup">
    <div class="container-fluid px-4">

        {{-- Logo / Brand --}}
        <a class="cup-brand" href="{{ route('dashboard') }}">
            <span class="cup-brand-mark">CUP</span>
            <span class="cup-brand-text">
                <div class="main">Sistema CUP</div>
                <div class="sub">UAGRM · FICCT</div>
            </span>
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
                        {{ request()->routeIs('periodos.*','carreras.*','materias.*','horarios.*','grupos.*') ? 'active' : '' }}"
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
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('horarios.*') ? 'fw-bold' : '' }}"
                               href="{{ route('horarios.index') }}">
                                <i class="bi bi-clock me-2 text-info"></i>Horarios
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('grupos.*') ? 'fw-bold' : '' }}"
                               href="{{ route('grupos.index') }}">
                                <i class="bi bi-people-fill me-2 text-primary"></i>Grupos
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Admisión (CU24-CU27) --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle
                        {{ request()->routeIs('admision.*','reportes.*','estadisticas.*') ? 'active' : '' }}"
                       href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-graph-up me-1"></i>Admisión
                    </a>
                    <ul class="dropdown-menu">
                        <li><span class="nav-section">Asignación y resultados</span></li>
                        <li><a class="dropdown-item {{ request()->routeIs('admision.preasignacion') ? 'fw-bold' : '' }}" href="{{ route('admision.preasignacion') }}">
                            <i class="bi bi-list-ol me-2 text-primary"></i>Pre-asignación (ranking)</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('admision.resultados') ? 'fw-bold' : '' }}" href="{{ route('admision.resultados') }}">
                            <i class="bi bi-diagram-3 me-2 text-success"></i>Resultados de asignación</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('admision.admitidos') ? 'fw-bold' : '' }}" href="{{ route('admision.admitidos') }}">
                            <i class="bi bi-award me-2 text-warning"></i>Lista final admitidos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item {{ request()->routeIs('reportes.*') ? 'fw-bold' : '' }}" href="{{ route('reportes.index') }}">
                            <i class="bi bi-file-earmark-text me-2 text-danger"></i>Reportes (PDF/Excel/HTML)</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('estadisticas.*') ? 'fw-bold' : '' }}" href="{{ route('estadisticas.dashboard') }}">
                            <i class="bi bi-bar-chart me-2 text-info"></i>Estadísticas</a></li>
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

{{-- Modal global de confirmación (reemplaza al confirm() del navegador) --}}
<x-modal-confirmar />

@stack('scripts')
</body>
</html>