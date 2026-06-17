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

            @php
                $rolActual = strtolower(Auth::user()->rol?->nombre ?? '');
                $esPostulante = $rolActual === 'postulante';
                $esAdminOCoord = in_array($rolActual, ['administrador', 'coordinador cup']);
                $esAuditor = $rolActual === 'auditor';
                $esDocente = $rolActual === 'docente';
            @endphp

            {{-- Links principales --}}
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>

                {{-- ═══════════════════════════════════════════════ --}}
                {{-- MENÚ SOLO PARA ADMINISTRADOR Y COORDINADOR CUP --}}
                {{-- ═══════════════════════════════════════════════ --}}
                @if($esAdminOCoord)

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
                        <li><a class="dropdown-item {{ request()->routeIs('notas.*') ? 'fw-bold' : '' }}" href="{{ route('notas.index') }}">
                            <i class="bi bi-pencil-square me-2 text-primary"></i>Registrar notas (CU21/22)</a></li>
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

                @endif {{-- fin @if($esAdminOCoord) --}}

                {{-- ═══════════════════════════════════════════ --}}
                {{-- MENÚ AUDITOR: solo lectura                  --}}
                {{-- ═══════════════════════════════════════════ --}}
                @if($esAuditor)
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle
                        {{ request()->routeIs('admision.*','reportes.*','estadisticas.*') ? 'active' : '' }}"
                       href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-graph-up me-1"></i>Reportes
                    </a>
                    <ul class="dropdown-menu">
                        <li><span class="nav-section">Solo lectura</span></li>
                        <li><a class="dropdown-item" href="{{ route('admision.preasignacion') }}">
                            <i class="bi bi-list-ol me-2 text-primary"></i>Pre-asignación (ranking)</a></li>
                        <li><a class="dropdown-item" href="{{ route('admision.resultados') }}">
                            <i class="bi bi-diagram-3 me-2 text-success"></i>Resultados</a></li>
                        <li><a class="dropdown-item" href="{{ route('admision.admitidos') }}">
                            <i class="bi bi-award me-2 text-warning"></i>Lista admitidos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('reportes.index') }}">
                            <i class="bi bi-file-earmark-text me-2 text-danger"></i>Reportes</a></li>
                        <li><a class="dropdown-item" href="{{ route('estadisticas.dashboard') }}">
                            <i class="bi bi-bar-chart me-2 text-info"></i>Estadísticas</a></li>
                    </ul>
                </li>
                @endif {{-- fin @if($esAuditor) --}}

                {{-- ═══════════════════════════════════════════ --}}
                {{-- MENÚ POSTULANTE: solo su información        --}}
                {{-- ═══════════════════════════════════════════ --}}
                @if($esPostulante)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('mis-grupos.*') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}">
                        <i class="bi bi-calendar-check me-1"></i>Mi Grupo y Horario
                    </a>
                </li>
                @endif {{-- fin @if($esPostulante) --}}

                {{-- ═══════════════════════════════════════════ --}}
                {{-- MENÚ DOCENTE: solo su información           --}}
                {{-- ═══════════════════════════════════════════ --}}
                @if($esDocente)
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <i class="bi bi-calendar2-week me-1"></i>Mis Grupos
                    </a>
                </li>
                @endif {{-- fin @if($esDocente) --}}

                {{-- Seguridad (solo Admin) --}}
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

{{-- ═══════════════════════════════════════════ --}}
{{-- ASISTENTE IA FLOTANTE                        --}}
{{-- Visible solo para: Administrador, Coordinador CUP, Auditor --}}
{{-- ═══════════════════════════════════════════ --}}
@auth
@php
  $rolIA = strtolower(Auth::user()->rol?->nombre ?? '');
  $puedeUsarIA = in_array($rolIA, ['administrador', 'coordinador cup', 'auditor']);
@endphp
@if($puedeUsarIA)

<style>
#ia-widget{position:fixed;bottom:24px;right:24px;z-index:9990;font-family:inherit}
.ia-fab{width:52px;height:52px;border-radius:50%;background:var(--cup-primary-light);color:#fff;border:none;font-size:1.35rem;cursor:pointer;box-shadow:0 4px 18px rgba(0,0,0,.28);display:flex;align-items:center;justify-content:center;transition:background .2s,transform .2s;outline:none}
.ia-fab:hover{background:var(--cup-primary);transform:scale(1.08)}
.ia-fab.open{background:var(--cup-primary)}
.ia-panel{position:absolute;bottom:62px;right:0;width:350px;max-width:calc(100vw - 40px);background:#fff;border-radius:16px;box-shadow:0 8px 36px rgba(0,0,0,.18);display:flex;flex-direction:column;overflow:hidden;border:1px solid var(--cup-border);animation:ia-slideup .2s ease}
@keyframes ia-slideup{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
.ia-header{background:var(--cup-primary);color:#fff;padding:11px 16px;display:flex;align-items:center;justify-content:space-between;font-weight:600;font-size:.9rem;gap:8px}
.ia-header-title{display:flex;align-items:center;gap:8px}
.ia-close{background:none;border:none;color:rgba(255,255,255,.8);font-size:1.1rem;cursor:pointer;padding:0;line-height:1;transition:color .15s}
.ia-close:hover{color:#fff}
.ia-messages{flex:1;overflow-y:auto;padding:14px 12px;display:flex;flex-direction:column;gap:8px;min-height:180px;max-height:300px;scroll-behavior:smooth}
.ia-msg{max-width:88%;padding:8px 13px;border-radius:14px;font-size:.84rem;line-height:1.55;word-break:break-word}
.ia-msg.user{background:var(--cup-primary-light);color:#fff;align-self:flex-end;border-bottom-right-radius:4px}
.ia-msg.bot{background:#f0f4f9;color:var(--cup-text);align-self:flex-start;border-bottom-left-radius:4px;white-space:pre-wrap}
.ia-msg.error{background:#fee2e2;color:#991b1b;align-self:flex-start}
.ia-typing{align-self:flex-start;display:flex;gap:5px;padding:10px 14px;background:#f0f4f9;border-radius:14px;border-bottom-left-radius:4px}
.ia-typing span{width:7px;height:7px;background:#94a3b8;border-radius:50%;animation:ia-bounce 1.1s ease-in-out infinite}
.ia-typing span:nth-child(2){animation-delay:.18s}
.ia-typing span:nth-child(3){animation-delay:.36s}
@keyframes ia-bounce{0%,80%,100%{transform:translateY(0)}40%{transform:translateY(-7px)}}
.ia-footer{display:flex;gap:6px;padding:9px 10px;border-top:1px solid var(--cup-border);background:#fafafa;align-items:center}
.ia-input{flex:1;border:1px solid var(--cup-border);border-radius:20px;padding:7px 14px;font-size:.84rem;outline:none;background:#fff;color:var(--cup-text);transition:border .15s}
.ia-input:focus{border-color:var(--cup-accent);box-shadow:0 0 0 3px rgba(44,123,229,.12)}
.ia-btn{width:34px;height:34px;border-radius:50%;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:.95rem;transition:background .15s,transform .15s;flex-shrink:0;outline:none}
.ia-btn:active{transform:scale(.92)}
.ia-btn.mic{background:#e5e7eb;color:var(--cup-muted)}
.ia-btn.mic.recording{background:#dc2626;color:#fff;animation:ia-pulse 1.2s ease infinite}
.ia-btn.send{background:var(--cup-primary-light);color:#fff}
.ia-btn.send:hover{background:var(--cup-primary)}
.ia-btn:disabled{opacity:.5;cursor:not-allowed}
@keyframes ia-pulse{0%,100%{box-shadow:0 0 0 0 rgba(220,38,38,.4)}60%{box-shadow:0 0 0 8px rgba(220,38,38,0)}}
.ia-badge{font-size:.65rem;background:rgba(255,255,255,.2);padding:1px 6px;border-radius:8px;letter-spacing:.3px}
</style>

<div id="ia-widget">
  <button id="ia-fab" class="ia-fab" title="Asistente IA CUP" aria-label="Abrir asistente IA">
    <i class="bi bi-stars"></i>
  </button>

  <div id="ia-panel" hidden>
    <div class="ia-header">
      <span class="ia-header-title">
        <i class="bi bi-stars"></i>
        Asistente CUP
        <span class="ia-badge">IA</span>
      </span>
      <button id="ia-close" class="ia-close" aria-label="Cerrar"><i class="bi bi-x-lg"></i></button>
    </div>

    <div id="ia-msgs" class="ia-messages" role="log" aria-live="polite">
      <div class="ia-msg bot">Hola. Soy el asistente IA del sistema CUP-FICCT.<br>Puedes preguntarme sobre postulantes, grupos, docentes, estadísticas o resultados de admisión.<br><br>Escribe tu consulta o usa el micrófono.</div>
    </div>

    <div class="ia-footer">
      <input id="ia-input" class="ia-input" type="text" placeholder="Consulta o dicta…" maxlength="500" autocomplete="off" aria-label="Consulta al asistente">
      <button id="ia-mic" class="ia-btn mic" title="Dictado por voz" aria-label="Activar micrófono"><i class="bi bi-mic-fill"></i></button>
      <button id="ia-send" class="ia-btn send" title="Enviar" aria-label="Enviar consulta"><i class="bi bi-send-fill"></i></button>
    </div>
  </div>
</div>

<script>
(function () {
  'use strict';

  const fab   = document.getElementById('ia-fab');
  const panel = document.getElementById('ia-panel');
  const close = document.getElementById('ia-close');
  const input = document.getElementById('ia-input');
  const send  = document.getElementById('ia-send');
  const mic   = document.getElementById('ia-mic');
  const msgs  = document.getElementById('ia-msgs');
  const CSRF  = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

  // ── Toggle panel ───────────────────────────────────────────────
  fab.addEventListener('click', () => {
    const abierto = !panel.hidden;
    panel.hidden = abierto;
    fab.classList.toggle('open', !abierto);
    fab.setAttribute('aria-expanded', String(!abierto));
    if (!abierto) { input.focus(); }
  });

  close.addEventListener('click', () => {
    panel.hidden = true;
    fab.classList.remove('open');
    fab.setAttribute('aria-expanded', 'false');
  });

  // Cerrar con Escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !panel.hidden) {
      panel.hidden = true;
      fab.classList.remove('open');
    }
  });

  // ── Speech Recognition (nativo) ────────────────────────────────
  const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
  let recognition = null;
  let grabando    = false;

  if (SR) {
    recognition = new SR();
    recognition.lang           = 'es-BO';
    recognition.interimResults = true;
    recognition.continuous     = false;
    recognition.maxAlternatives = 1;

    recognition.onresult = ({ results }) => {
      const segmentos  = Array.from(results);
      const raw        = segmentos.map(r => r[0].transcript).join('');
      const esFinal    = segmentos.every(r => r.isFinal);

      if (esFinal) {
        // Limpiar puntuación automática del navegador y asegurar que sea pregunta
        let limpio = raw
          .trim()
          .replace(/^[¿¡\s]+/, '')          // quitar ¿ o ¡ del inicio
          .replace(/[.,;:!?¿¡\s]+$/, '')    // quitar puntuación al final
          .trim();

        // Normalizar transcripciones erróneas del reconocedor de voz en español:
        // "CI" se escucha como "chi", "ce i", "si" (cuando va seguido de números)
        limpio = limpio
          .replace(/\bchi\s+(\d)/gi,    'carnet $1')  // "chi 10000001" → "carnet 10000001"
          .replace(/\bchi(\d)/gi,        'carnet $1')  // "chi10000001"  → "carnet 10000001"
          .replace(/\bce[- ]?[ií]\b/gi, 'carnet')     // "ce i" / "ce-i" → "carnet"
          .replace(/\bce[- ]?i\s+(\d)/gi,'carnet $1') // "ce i 10000001" → "carnet 10000001"
          // Alias verbales: "el número" / "número de carnet" antes de dígitos
          .replace(/n[uú]mero\s+de\s+carnet\s*/gi, 'carnet ')
          .replace(/n[uú]mero\s+carnet\s*/gi,       'carnet ');

        input.value = limpio ? limpio + '?' : '';
      } else {
        // Resultado intermedio: mostrar en tiempo real sin modificar
        input.value = raw;
      }
    };

    recognition.onend = () => {
      grabando = false;
      mic.classList.remove('recording');
      mic.innerHTML = '<i class="bi bi-mic-fill"></i>';
      mic.title = 'Dictado por voz';
      // Pequeño delay para que onresult termine de escribir el valor final
      setTimeout(() => { if (input.value.trim()) enviar(); }, 80);
    };

    recognition.onerror = (e) => {
      grabando = false;
      mic.classList.remove('recording');
      mic.innerHTML = '<i class="bi bi-mic-fill"></i>';
      mic.title = 'Dictado por voz';

      const mensajes = {
        'not-allowed'       : '⚠️ Micrófono bloqueado. El dictado por voz requiere HTTPS. En desarrollo puedes escribir tu consulta en el cuadro de texto.',
        'service-not-allowed': '⚠️ El navegador no permite el micrófono en sitios HTTP. Usa el cuadro de texto para escribir tu consulta.',
        'network'           : 'Error de red con el servicio de voz. Escribe tu consulta en el cuadro de texto.',
        'audio-capture'     : 'No se detectó micrófono en este dispositivo.',
        'aborted'           : null, // cancelado por el usuario, no mostrar nada
        'no-speech'         : null, // silencio, no mostrar nada
      };

      const msg = mensajes[e.error];
      if (msg) agregarMsg(msg, 'error');
      else if (!(e.error in mensajes)) {
        agregarMsg('Error de micrófono (' + e.error + '). Escribe tu consulta en el cuadro de texto.', 'error');
      }
    };
  } else {
    mic.hidden = true; // navegador sin soporte
  }

  // Aviso proactivo si el sitio corre sobre HTTP (mic no funcionará en Chrome/Edge)
  const esHTTP = location.protocol === 'http:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1';
  if (esHTTP && recognition) {
    mic.title = 'El dictado por voz requiere HTTPS. Escribe tu consulta en el cuadro de texto.';
    mic.style.opacity = '0.45';
    mic.style.cursor  = 'not-allowed';
    mic.addEventListener('click', (e) => {
      e.stopImmediatePropagation();
      agregarMsg('⚠️ El dictado por voz requiere HTTPS. En desarrollo, escribe tu consulta directamente en el cuadro de texto.', 'error');
    }, true);
  }

  if (!esHTTP) {
    mic.addEventListener('click', () => {
      if (!recognition) return;
      if (grabando) {
        recognition.stop();
      } else {
        input.value = '';
        grabando = true;
        mic.classList.add('recording');
        mic.innerHTML = '<i class="bi bi-mic-mute-fill"></i>';
        mic.title = 'Grabando… (clic para detener)';
        recognition.start();
      }
    });
  }

  // ── Enviar consulta ────────────────────────────────────────────
  input.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); enviar(); }
  });
  send.addEventListener('click', enviar);

  function agregarMsg(texto, tipo) {
    const div = document.createElement('div');
    div.className = 'ia-msg ' + tipo;
    div.textContent = texto;
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
    return div;
  }

  function mostrarTyping() {
    const div = document.createElement('div');
    div.className = 'ia-typing';
    div.innerHTML = '<span></span><span></span><span></span>';
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
    return div;
  }

  async function enviar() {
    const texto = input.value.trim();
    if (!texto || send.disabled) return;

    agregarMsg(texto, 'user');
    input.value = '';
    send.disabled = true;
    mic.disabled  = true;

    const typing = mostrarTyping();

    try {
      const res = await fetch('{{ route("asistente-ia.consultar") }}', {
        method : 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF,
          'Accept'      : 'application/json',
        },
        body: JSON.stringify({ mensaje: texto }),
      });

      const data = await res.json();
      typing.remove();

      if (res.ok) {
        agregarMsg(data.respuesta ?? 'Sin respuesta.', 'bot');
      } else {
        agregarMsg(data.error ?? 'Error al procesar la consulta.', 'error');
      }
    } catch (_) {
      typing.remove();
      agregarMsg('Error de red. Verifica tu conexión e inténtalo de nuevo.', 'error');
    } finally {
      send.disabled = false;
      mic.disabled  = false;
      input.focus();
    }
  }
})();
</script>

@endif
@endauth
</body>
</html>