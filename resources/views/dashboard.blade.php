@extends('layouts.base')

@section('titulo', 'Dashboard')

@section('contenido')

@php $p = \App\Models\Periodo::where('activo', true)->first(); @endphp

<div class="page-header mb-4">
  <h1><i class="bi bi-speedometer2 me-2"></i>Panel de Administración — CUP FICCT</h1>
  <p class="page-subtitle">
    Periodo activo: <strong>{{ $p ? $p->fecha_ini_curso->format('Y') . ' — Gestión 1' : 'Sin periodo activo' }}</strong>
    @auth — Bienvenido, <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->rol?->nombre ?? 'Sin rol' }})@endauth
  </p>
</div>

{{-- TARJETAS KPI --}}
<div class="row g-3 mb-4">
  <div class="col-md-6 col-lg-3">
    <a href="{{ route('postulantes.index') }}" class="text-decoration-none text-reset d-block h-100">
      <div class="kpi-card kpi-primary h-100">
        <div class="kpi-icon"><i class="bi bi-people"></i></div>
        <div class="kpi-value">{{ \App\Models\Postulante::count() }}</div>
        <div class="kpi-label">Postulantes</div>
      </div>
    </a>
  </div>
  <div class="col-md-6 col-lg-3">
    <a href="{{ route('docentes.index') }}" class="text-decoration-none text-reset d-block h-100">
      <div class="kpi-card kpi-success h-100">
        <div class="kpi-icon"><i class="bi bi-person-workspace"></i></div>
        <div class="kpi-value">{{ \App\Models\Docente::count() }}</div>
        <div class="kpi-label">Docentes</div>
      </div>
    </a>
  </div>
  <div class="col-md-6 col-lg-3">
    <a href="{{ route('carreras.index') }}" class="text-decoration-none text-reset d-block h-100">
      <div class="kpi-card kpi-warning h-100">
        <div class="kpi-icon"><i class="bi bi-journal-bookmark"></i></div>
        <div class="kpi-value">{{ \App\Models\Carrera::count() }}</div>
        <div class="kpi-label">Carreras</div>
      </div>
    </a>
  </div>
  <div class="col-md-6 col-lg-3">
    <a href="{{ route('documentos.index') }}" class="text-decoration-none text-reset d-block h-100">
      <div class="kpi-card kpi-danger h-100">
        <div class="kpi-icon"><i class="bi bi-folder2-open"></i></div>
        <div class="kpi-value">{{ \App\Models\DocumentoPostulante::where('estado','pendiente')->count() }}</div>
        <div class="kpi-label">Docs pendientes</div>
      </div>
    </a>
  </div>

  @auth
  @if(Auth::user()->tienePermiso('usuarios.ver'))
  <div class="col-md-6 col-lg-3">
    <a href="{{ route('usuarios.index') }}" class="text-decoration-none text-reset d-block h-100">
      <div class="kpi-card kpi-accent h-100">
        <div class="kpi-icon"><i class="bi bi-people-fill"></i></div>
        <div class="kpi-value">{{ \App\Models\User::count() }}</div>
        <div class="kpi-label">Usuarios</div>
      </div>
    </a>
  </div>
  @endif
  @if(Auth::user()->tienePermiso('roles.ver'))
  <div class="col-md-6 col-lg-3">
    <a href="{{ route('roles.index') }}" class="text-decoration-none text-reset d-block h-100">
      <div class="kpi-card kpi-info h-100">
        <div class="kpi-icon"><i class="bi bi-person-badge"></i></div>
        <div class="kpi-value">{{ \App\Models\Rol::count() }}</div>
        <div class="kpi-label">Roles</div>
      </div>
    </a>
  </div>
  @endif
  @if(Auth::user()->tienePermiso('bitacora.ver'))
  <div class="col-md-6 col-lg-3">
    <a href="{{ route('bitacora.index') }}" class="text-decoration-none text-reset d-block h-100">
      <div class="kpi-card kpi-primary h-100">
        <div class="kpi-icon"><i class="bi bi-journal-text"></i></div>
        <div class="kpi-value">{{ \App\Models\Bitacora::whereDate('created_at', today())->count() }}</div>
        <div class="kpi-label">Eventos hoy</div>
      </div>
    </a>
  </div>
  @endif
  @endauth
</div>

{{-- ACCESOS RÁPIDOS + ACTIVIDAD RECIENTE --}}
<div class="row g-3 mt-1">
  <div class="col-lg-7">
    <div class="panel-cup h-100">
      <div class="panel-cup-header">
        <strong><i class="bi bi-rocket-takeoff me-1"></i> Accesos rápidos</strong>
      </div>
      <div class="panel-cup-body">
        <div class="row g-2">
          @php
            $accesos = [
              ['route'=>'usuarios.index','icon'=>'bi-people','title'=>'Usuarios','desc'=>'Gestionar cuentas del sistema','color'=>'#1e5fa8'],
              ['route'=>'roles.index','icon'=>'bi-shield-lock','title'=>'Roles y Permisos','desc'=>'Configurar accesos','color'=>'#198754'],
              ['route'=>'aulas.index','icon'=>'bi-door-open','title'=>'Aulas','desc'=>'Catálogo de aulas','color'=>'#2c7be5'],
              ['route'=>'bitacora.index','icon'=>'bi-journal-text','title'=>'Bitácora','desc'=>'Auditoría del sistema','color'=>'#f59e0b'],
            ];
            if (Route::has('postulantes.index')) {
              $accesos[] = ['route'=>'postulantes.index','icon'=>'bi-person-vcard','title'=>'Postulantes','desc'=>'Inscripciones al CUP','color'=>'#dc2626'];
            }
            if (Route::has('docentes.index')) {
              $accesos[] = ['route'=>'docentes.index','icon'=>'bi-person-workspace','title'=>'Docentes','desc'=>'Plantilla docente','color'=>'#0dcaf0'];
            }
          @endphp

          @foreach($accesos as $a)
          <div class="col-md-6">
            <a href="{{ route($a['route']) }}"
               class="d-flex align-items-center gap-3 p-3 text-decoration-none text-reset rounded"
               style="background:#f8f9fb;border:1px solid var(--cup-border);transition:all .15s;"
               onmouseover="this.style.background='#fff';this.style.borderColor='{{ $a['color'] }}';this.style.transform='translateX(4px)';"
               onmouseout="this.style.background='#f8f9fb';this.style.borderColor='var(--cup-border)';this.style.transform='translateX(0)';">
              <div style="width:42px;height:42px;border-radius:10px;background:{{ $a['color'] }}15;display:flex;align-items:center;justify-content:center;color:{{ $a['color'] }};">
                <i class="bi {{ $a['icon'] }} fs-4"></i>
              </div>
              <div>
                <div style="font-weight:600;">{{ $a['title'] }}</div>
                <small class="text-muted">{{ $a['desc'] }}</small>
              </div>
            </a>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="panel-cup h-100">
      <div class="panel-cup-header">
        <strong><i class="bi bi-clock-history me-1"></i> Actividad reciente</strong>
        <a href="{{ route('bitacora.index') }}" class="btn btn-sm btn-outline-secondary">
          Ver toda <i class="bi bi-arrow-right ms-1"></i>
        </a>
      </div>
      <div class="panel-cup-body p-0">
        @php
          try {
            $recientes = \App\Models\Bitacora::with('user')->latest('id')->limit(5)->get();
          } catch (\Exception $e) {
            $recientes = collect();
          }
        @endphp

        @if($recientes->isEmpty())
          <p class="text-muted text-center py-3 mb-0">Sin actividad reciente</p>
        @else
          <div style="max-height:320px;overflow-y:auto;">
            @foreach($recientes as $r)
              @php
                $verdes = ['LOGIN_OK','USUARIO_CREADO','ROL_CREADO','AULA_CREADA','CREAR','ACTIVAR'];
                $rojos = ['LOGIN_FAIL','LOGIN_INACTIVO','ACCESO_DENEGADO','ELIMINAR'];
                $color = in_array($r->accion, $verdes) ? '#198754'
                       : (in_array($r->accion, $rojos) ? '#dc2626' : '#f59e0b');
              @endphp
              <div class="d-flex gap-2 align-items-start p-3" style="border-bottom:1px solid var(--cup-border);">
                <div style="width:8px;height:8px;border-radius:50%;background:{{ $color }};margin-top:8px;flex-shrink:0;"></div>
                <div class="flex-grow-1">
                  <small style="font-weight:600;">{{ $r->user->name ?? '— Sistema —' }}</small>
                  <span class="badge-cup badge-modulo" style="font-size:0.65rem;">{{ $r->accion }}</span>
                  <div><small class="text-muted">{{ \Illuminate\Support\Str::limit($r->descripcion, 60) }}</small></div>
                  <small class="text-muted" style="font-size:0.7rem;">
                    <i class="bi bi-clock"></i> {{ $r->created_at->diffForHumans() }}
                  </small>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

@endsection
