# Informe Técnico — Sistema CUP UAGRM
## Guía de estudio para defensa (uso interno)

> Este documento **no** es el informe formal del proyecto. Es un manual interno
> tipo "guía de estudio" para Ariany y NyxM4x: explica dónde vive cada cosa, qué
> pasa si tocás tal archivo, y cómo defender cada parte del sistema.
> Pensado para leerse de corrido en ~30-45 min.

---

### Tabla de contenidos

1. Resumen ejecutivo
2. Estructura de carpetas
3. Arquitectura — cómo fluye un request
4. Módulos implementados (14 CUs)
5. Base de datos
6. Guía rápida: ¿dónde modifico si me piden…?
7. Archivos críticos
8. Sistema de permisos
9. Sistema de bitácora
10. Casos de prueba de la defensa (demos)
11. Posibles preguntas del jurado
12. Pendientes de la entrega final (09/06)
13. Control de versiones (Git)
14. Limitaciones conocidas (leer antes de la defensa)

---

## 1. RESUMEN EJECUTIVO

- **Nombre:** Sistema de Admisión CUP — Curso Preuniversitario, FICCT-UAGRM.
- **Stack tecnológico real:**
  | Capa | Tecnología |
  |---|---|
  | Lenguaje | PHP 8.3 |
  | Framework | Laravel 11 + Laravel Breeze (scaffolding de auth) |
  | Base de datos | PostgreSQL |
  | Frontend | Blade + Bootstrap 5 (CDN) + Bootstrap Icons |
  | Correo | SMTP Gmail (recuperación de contraseña) |
  | Servidor local | Laragon (`php artisan serve` en 127.0.0.1:8000) |
- **Integrantes y roles:**
  - **Ariany Claure** — Seguridad y auditoría (CU01-CU06), Aulas (CU10), rediseño UI/UX, features de seguridad de contraseña.
  - **NyxM4x (Adalid Gragedа)** — Gestión académica (CU07-CU09, CU11) y postulantes (CU12-CU14).
- **Total de CUs implementados:** 14 (CU01 a CU14).
- **Estado actual:** funcional. UI unificada (Bootstrap, paleta institucional). BD poblada con datos demo (500 postulantes). Pendientes los módulos de la entrega final (grupos, pagos, notas, admisión, reportes).

---

## 2. ESTRUCTURA DE CARPETAS DEL PROYECTO

```
cup-admision-laravel/
├── app/
│   ├── Domain/                      # "Clean-ish Architecture" (solo módulos de Ariany)
│   │   ├── Seguridad/
│   │   │   ├── UseCases/            # AutenticarUsuario, CerrarSesion, CRUD de Roles
│   │   │   └── Repositories/        # RolRepository
│   │   ├── Usuarios/
│   │   │   ├── UseCases/            # Crear/Editar/Inactivar/Reactivar/Listar usuario
│   │   │   └── Repositories/        # UsuarioRepository
│   │   ├── Bitacora/
│   │   │   ├── Services/            # BitacoraLogger (escribe eventos)
│   │   │   ├── Repositories/        # BitacoraRepository (lee/filtra)
│   │   │   └── UseCases/            # ConsultarBitacoraUseCase
│   │   └── GestionGlobal/
│   │       └── Aulas/{UseCases,Repositories}
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                # Breeze: login, password reset, etc.
│   │   │   ├── Seguridad/           # Usuario/Rol/Permiso controllers
│   │   │   ├── Bitacora/            # BitacoraController (solo lectura)
│   │   │   ├── GestionGlobal/       # AulaController
│   │   │   └── *Controller.php      # MVC clásico del compañero (Periodo, Carrera, Materia, etc.)
│   │   ├── Middleware/              # ExigirPermiso (+ ForzarCambioPassword en otra rama)
│   │   └── Requests/                # FormRequests de validación (Seguridad, GestionGlobal, Auth)
│   └── Models/                      # 17 modelos Eloquent
├── database/
│   ├── migrations/                 # 23 migraciones
│   └── seeders/                    # SeguridadSeeder, AulasSeeder, DatabaseSeeder, DatosDemoSeeder
├── resources/views/
│   ├── layouts/                    # base.blade.php (maestro Bootstrap) + guest.blade.php (auth)
│   ├── auth/                       # login, register, forgot/reset password, etc.
│   ├── dashboards/                 # 1 por rol: admin, coordinador, docente, postulante, auditor
│   ├── seguridad/                  # usuarios/, roles/, permisos/
│   ├── gestion-global/aulas/       # index, create, edit
│   ├── bitacora/                   # index, show
│   ├── postulantes/ docentes/ carreras/ materias/ periodos/ requisitos/ documentos/
│   └── emails/                     # recuperar-password (plantilla del mail, en rama auth)
└── routes/
    └── web.php                     # Todas las rutas web (+ require auth.php de Breeze)
```

> **Dato clave de arquitectura:** Ariany usa **Domain/UseCases/Repositories** (capa de
> aplicación separada). El compañero (CU07-CU14) usa **MVC clásico**: el controller
> habla directo con el modelo Eloquent. Ambos conviven; saberlo evita confusión en la defensa.

---

## 3. ARQUITECTURA — CÓMO FLUYE UN REQUEST

Ejemplo real: **crear un usuario** (`POST /seguridad/usuarios`).

```
Navegador (form submit)
   │
   ▼
routes/web.php                     ──► define la ruta + middleware
   │   Route::post('/usuarios', [UsuarioController,'store'])
   │       ->middleware('permiso:usuarios.crear')
   ▼
Middleware ExigirPermiso           ──► ¿el rol tiene 'usuarios.crear'? (Admin pasa siempre)
   │                                    si no → bitácora ACCESO_DENEGADO + 403
   ▼
UsuarioController@store
   │
   ▼
StoreUsuarioRequest                ──► valida datos (email único, password, etc.)
   │
   ▼
CrearUsuarioUseCase@ejecutar       ──► REGLAS DE NEGOCIO
   │   - revalida unicidad email/CI
   │   - hashea password
   │   - fuerza debe_cambiar_password = true (rama auth)
   ▼
UsuarioRepository / Model User     ──► User::create(...) → INSERT
   │
   ▼
BitacoraLogger::registrar(...)     ──► escribe evento USUARIO_CREADO
   │
   ▼
redirect()->route('usuarios.index')->with('success', ...)
```

**Comparación con un módulo del compañero (MVC clásico), ej. crear materia:**

```
routes/web.php (Route::resource('materias'))
   ▼
MateriaController@store
   ▼
$request->validate([...])          ──► validación inline (sin FormRequest)
   ▼
Materia::create([...])             ──► el controller habla directo al modelo
   ▼
BitacoraLogger::registrar('CREAR','Materias',...)
   ▼
redirect()->route('materias.index')
```

La diferencia: en los módulos de Ariany la lógica vive en el **UseCase**; en los del
compañero vive en el **controller**. Para la defensa: "usamos una capa de aplicación
(UseCases) en los módulos core de seguridad para separar reglas de negocio del HTTP".

---

## 4. MÓDULOS IMPLEMENTADOS (14 CUs)

> Permisos: solo los módulos de **Seguridad** y **Aulas** están protegidos por
> `middleware('permiso:...')`. Los módulos académicos del compañero (CU07-CU14) hoy
> **no** tienen middleware (ver sección 14).

### CU01 — Iniciar sesión
- **Dueño:** Ariany
- **Ruta:** `GET/POST /login`
- **Controller:** `Auth/AuthenticatedSessionController.php`
- **UseCase:** `Domain/Seguridad/UseCases/AutenticarUsuarioUseCase.php`
- **Vista:** `auth/login.blade.php` (split-screen institucional)
- **Reglas clave:** valida credenciales, **rechaza usuarios inactivos** (logout inmediato), regenera sesión (anti session-fixation), **redirige según rol**.
- **Bitácora:** `LOGIN_OK`, `LOGIN_FAIL`, `LOGIN_INACTIVO`.

### CU02 — Cerrar sesión
- **Dueño:** Ariany
- **Ruta:** `POST /logout`
- **Controller:** `Auth/AuthenticatedSessionController@destroy`
- **UseCase:** `CerrarSesionUseCase.php`
- **Bitácora:** `LOGOUT_OK`.

### CU03 — Gestionar Usuarios
- **Dueño:** Ariany
- **Ruta:** `/seguridad/usuarios`
- **Controller:** `Seguridad/UsuarioController.php`
- **Vistas:** `seguridad/usuarios/{index,create,edit}`
- **Modelo:** `User.php` · **Tabla:** `users`
- **UseCases:** Crear/Editar/Inactivar/Reactivar/Listar (en `Domain/Usuarios/UseCases`)
- **Permisos:** `usuarios.ver/crear/editar/eliminar`
- **Reglas:** email y CI únicos; password hasheado; inactivación lógica (no se borra).
- **Bitácora:** `USUARIO_CREADO/EDITADO/INACTIVADO/REACTIVADO`.

### CU04 — Gestionar Roles
- **Dueño:** Ariany · **Ruta:** `/seguridad/roles`
- **Controller:** `Seguridad/RolController.php` · **Modelo:** `Rol.php` · **Tabla:** `roles`
- **UseCases:** Crear/Editar/Inactivar/Reactivar/Listar (`Domain/Seguridad/UseCases`)
- **Permisos:** `roles.ver/crear/editar/eliminar`
- **Reglas:** el rol **Administrador es protegido** (no se inactiva, nombre readonly, conserva todos los permisos); asignación de permisos vía pivot `rol_permiso`.
- **Bitácora:** `ROL_CREADO/EDITADO/INACTIVADO/REACTIVADO`.

### CU05 — Gestionar Permisos
- **Dueño:** Ariany · **Ruta:** `/seguridad/permisos` y `/seguridad/permisos/matriz`
- **Controller:** `Seguridad/PermisoController.php` · **Modelo:** `Permiso.php` · **Tabla:** `permisos`
- **Permiso:** `permisos.gestionar`
- **Reglas:** catálogo **solo lectura** (los permisos los define el código, no se crean dinámicamente). La **matriz** muestra rol × permiso.

### CU06 — Bitácora del sistema
- **Dueño:** Ariany · **Ruta:** `/bitacora` (index + show)
- **Controller:** `Bitacora/BitacoraController.php` · **Modelo:** `Bitacora.php` · **Tabla:** `bitacora`
- **UseCase/Repos:** `ConsultarBitacoraUseCase`, `BitacoraRepository`, `BitacoraLogger` (Service)
- **Permiso:** `bitacora.ver`
- **Reglas:** **inmutable** (solo lectura, sin editar/borrar); filtros por usuario, módulo, acción, fechas y búsqueda. KPIs de estadísticas.

### CU07 — Periodos académicos
- **Dueño:** NyxM4x · **Ruta:** `/periodos` (resource)
- **Controller:** `PeriodoController.php` · **Modelo:** `Periodo.php` · **Tabla:** `periodos`
- **Reglas:** 4 fechas (inicio/fin de inscripción y de curso) + flag `activo`. Solo un periodo activo es el "vigente".

### CU08 — Carreras
- **Dueño:** NyxM4x · **Ruta:** `/carreras` (resource + reactivar)
- **Controller:** `CarreraController.php` · **Modelos:** `Carrera.php`, `CupoCarrera.php` · **Tablas:** `carreras`, `cupo_carreras`
- **Reglas:** cada carrera tiene **cupo máximo por periodo** (`cupo_carreras.cupo_max`); requiere periodo activo para asignar cupo.

### CU09 — Materias
- **Dueño:** NyxM4x (horario agregado por Ariany) · **Ruta:** `/materias`
- **Controller:** `MateriaController.php` · **Modelo:** `Materia.php` · **Tabla:** `materias`
- **Reglas:** sigla y nombre únicos; **pesos de 3 exámenes que deben sumar 100%**; **horario estructurado** (`dias_dictado` JSON + `hora_inicio`/`hora_fin`); columna legacy `dias` se mantiene derivada.
- **Bitácora:** `CREAR/EDITAR/DESACTIVAR/ACTIVAR` (módulo Materias).

### CU10 — Gestionar Aulas
- **Dueño:** Ariany · **Ruta:** `/gestion-global/aulas`
- **Controller:** `GestionGlobal/AulaController.php` · **Modelo:** `Aula.php` · **Tabla:** `aulas`
- **UseCases:** Crear/Editar/Inactivar/Reactivar/Listar (`Domain/GestionGlobal/Aulas`)
- **Permisos:** `aulas.ver/crear/editar/eliminar`
- **Reglas:** código de aula único; capacidad; filtros por edificio/estado; inactivación lógica.
- **Bitácora:** `AULA_CREADA/EDITADA/INACTIVADA/REACTIVADA`.

### CU11 — Requisitos de inscripción
- **Dueño:** NyxM4x · **Ruta:** `/requisitos`
- **Controller:** `RequisitoController.php` · **Modelo:** `Requisito.php` · **Tabla:** `requisitos`
- **Reglas:** por periodo; obligatorio/opcional; formato aceptado (PDF/JPG/PNG) y tamaño máximo.

### CU12 — Docentes
- **Dueño:** NyxM4x · **Ruta:** `/docentes`
- **Controller:** `DocenteController.php` · **Modelos:** `Docente.php`, `Persona.php`, `Profesion.php` · **Tablas:** `docentes`, `personas`, `profesiones`
- **Reglas:** un docente referencia a una **persona** + **profesión**; carga de certificados (PDF/JPG/PNG, multipart); años de experiencia.

### CU13 — Postulantes
- **Dueño:** NyxM4x · **Ruta:** `/postulantes` (resource)
- **Controller:** `PostulanteController.php` · **Modelos:** `Postulante.php`, `Persona.php`, `Inscripcion.php`, `PostulacionCarrera.php`
- **Tablas:** `postulantes`, `personas`, `inscripciones`, `postulacion_carreras`
- **Reglas:** al registrar se crea persona + postulante + **inscripción** al periodo activo + **carreras postuladas** (1ra obligatoria, 2da opcional, con prioridad). Requiere periodo activo.

### CU14 — Documentos de postulantes
- **Dueño:** NyxM4x · **Ruta:** `/documentos`
- **Controller:** `DocumentoPostulanteController.php` · **Modelo:** `DocumentoPostulante.php` · **Tabla:** `documentos_postulantes`
- **Reglas:** subir/reemplazar documento por requisito (multipart); **aprobar/rechazar** con comentario; estados pendiente/aprobado/rechazado.

### Extra (rama `feature/auth-mail-primera-vez`, sin mergear aún)
- **Recuperación de contraseña por email** (Gmail SMTP) con plantilla UAGRM.
- **Cambio obligatorio de contraseña en primer login** (middleware `ForzarCambioPassword` + flag `users.debe_cambiar_password`).

---

## 5. BASE DE DATOS

Conteo real de registros (al momento de generar este informe):

| Tabla | Registros | Para qué sirve |
|---|---:|---|
| users | 6 | Cuentas de acceso |
| roles | 6 | Roles del sistema |
| permisos | 14 | Permisos atómicos |
| rol_permiso | 26 | Pivot rol↔permiso |
| personas | 506 | Datos personales (postulantes + docentes) |
| postulantes | 500 | Aspirantes |
| inscripciones | 500 | Inscripción de postulante a un periodo |
| postulacion_carreras | 783 | Carreras postuladas (1ra/2da) |
| carreras | 5 | Carreras del CUP |
| cupo_carreras | 5 | Cupo por carrera/periodo |
| materias | 4 | Materias del curso (con horario) |
| docentes | 6 | Plantilla docente |
| profesiones | 6 | Profesiones de docentes |
| periodos | 1 | Periodos académicos (1 activo) |
| aulas | 15 | Aulas disponibles |
| requisitos | 0 | Requisitos de inscripción (vacío) |
| documentos_postulantes | 0 | Documentos cargados (vacío) |
| bitacora | 77 | Eventos de auditoría |
| sessions | 6 | Sesiones activas |
| migrations | 23 | Control de migraciones |
| cache / cache_locks / jobs / job_batches / failed_jobs | 0 | Infra Laravel |
| password_reset_tokens | 1 | Tokens de reseteo |

**Relaciones principales (FK):**
- `users.rol_id → roles.id`
- `rol_permiso.rol_id → roles.id`, `rol_permiso.permiso_id → permisos.id`
- `postulantes.persona_id → personas.id`
- `inscripciones.postulante_id → postulantes.id`, `inscripciones.periodo_id → periodos.id`
- `postulacion_carreras.inscripcion_id → inscripciones.id`, `…carrera_id → carreras.id`
- `cupo_carreras.carrera_id → carreras.id`, `…periodo_id → periodos.id`
- `docentes.persona_id → personas.id`, `docentes.profesion_id → profesiones.id`
- `documentos_postulantes.inscripcion_id → inscripciones.id`, `…requisito_id → requisitos.id`
- `bitacora.user_id → users.id` (nullable; eventos de sistema sin usuario)

```sql
-- Ejemplo de la consulta de "grupos" del dashboard de coordinador
SELECT COUNT(*) AS inscritos
FROM inscripciones
WHERE periodo_id = (SELECT id FROM periodos WHERE activo = true LIMIT 1);
-- grupos = CEIL(inscritos / 80)
```

---

## 6. GUÍA RÁPIDA: ¿DÓNDE MODIFICO SI ME PIDEN…?

| Si me piden… | Voy a archivo(s)… | Qué cambio |
|---|---|---|
| Agregar campo a Usuario | (1) migración `add_columns_to_users_table` o nueva, (2) `app/Models/User.php` (`#[Fillable]` + `casts()`), (3) `Http/Requests/Seguridad/StoreUsuarioRequest.php` y `UpdateUsuarioRequest.php`, (4) vistas `seguridad/usuarios/{create,edit}` | Agregar la columna + el campo en fillable + regla de validación + input |
| Cambiar validación de email | `Http/Requests/Seguridad/StoreUsuarioRequest.php` | Editar regla `'email' => [...]` |
| Agregar nuevo permiso | `database/seeders/SeguridadSeeder.php` (`$permisosData`) → re-seed | Agregar `['codigo'=>'x.y','modulo'=>...]` y asignarlo al rol |
| Cambiar regla de negocio al crear usuario | `app/Domain/Usuarios/UseCases/CrearUsuarioUseCase.php` | Editar `ejecutar()` |
| Cambiar apariencia de un listado | `resources/views/<modulo>/index.blade.php` | Editar la `<table class="table-cup">` |
| Agregar columna a una tabla en pantalla | El `index.blade.php` correspondiente | Agregar `<th>` + `<td>` (y ajustar `colspan` del estado vacío) |
| Cambiar el orden de un listado | El `Repository` (Ariany) o el `Controller@index` (compañero) | Editar el `orderBy(...)` |
| Cambiar el redirect post-login | `app/Domain/Seguridad/UseCases/AutenticarUsuarioUseCase.php` | Editar el mapa `REDIRECTS_POR_ROL` |
| Cambiar el mensaje de error de login | `AutenticarUsuarioUseCase.php` (texto "Credenciales incorrectas") | Editar el string del `resultado(false, ...)` |
| Modificar el header/navbar del sitio | `resources/views/layouts/base.blade.php` | Editar el `<nav class="navbar-cup">` |
| Agregar color a la paleta | `resources/views/layouts/base.blade.php` (`:root { --cup-* }`) | Agregar variable CSS y su clase |
| Hacer que un rol vea una pantalla | `routes/web.php` (middleware `permiso:xxx`) + asignar permiso en `SeguridadSeeder` | Quitar/ajustar el `permiso:` o dar el permiso al rol |
| Cambiar pesos/validación de materias | `app/Http/Controllers/MateriaController.php` | Editar `validate([...])` y la suma de pesos |
| Cambiar el horario de materias | `MateriaController` (validación) + `materias/{create,edit}` (checkboxes/horas) + accessors en `Materia.php` | — |
| Cambiar la fórmula de grupos | `resources/views/dashboards/coordinador.blade.php` (`ceil($totalInscritos / 80)`) | Cambiar el divisor 80 |
| Cambiar el diseño del email de reseteo | `resources/views/emails/recuperar-password.blade.php` (rama auth) | Editar el HTML |
| Cambiar credenciales SMTP / DB | `.env` (no versionado) | Editar `MAIL_*` / `DB_*` y `php artisan config:clear` |
| Agregar acción a la bitácora | El `UseCase`/`Controller` donde ocurre + llamar `BitacoraLogger::registrar(...)` | Agregar la llamada con un código de acción nuevo |

---

## 7. ARCHIVOS CRÍTICOS — NO TOCAR SIN ENTENDER

| Archivo | Qué hace | Qué pasa si se rompe |
|---|---|---|
| `resources/views/layouts/base.blade.php` | Layout maestro: navbar, paleta CSS y componentes (`.kpi-card`, `.panel-cup`, `.table-cup`, badges, botones) de **todas** las vistas internas | Se cae el estilo de TODO el sistema |
| `resources/views/layouts/guest.blade.php` | Layout de las pantallas de auth (login, reset, etc.) | Se rompen todas las pantallas sin sesión |
| `routes/web.php` | Define todas las rutas web y sus middleware | Rutas 404 / sin protección |
| `bootstrap/app.php` | Registra middleware (alias `permiso`, y en rama auth `cambio.password`) | Los middleware dejan de aplicarse |
| `app/Models/User.php` | Modelo de autenticación (fillable, casts, relación rol, `tienePermiso`) | Falla login y chequeo de permisos |
| `config/auth.php` | Config de guards/providers de Laravel | Falla autenticación |
| `.env` | Credenciales DB + SMTP + APP_KEY | No conecta a BD / no envía mails |
| `app/Providers/AppServiceProvider.php` | `Paginator::useBootstrapFive()` | La paginación vuelve al estilo Tailwind (roto) |
| `database/seeders/SeguridadSeeder.php` | Crea admin, roles, permisos y asignaciones | Se pierde el esquema de seguridad al re-sembrar |
| `app/Http/Middleware/ExigirPermiso.php` | Valida permisos por ruta (admin bypass) | Cualquiera accedería a Seguridad/Aulas |

---

## 8. SISTEMA DE PERMISOS — CÓMO FUNCIONA

**Roles (5):** Administrador, Coordinador CUP, Docente, Postulante, Auditor.

**Permisos (14):** 10 del módulo Seguridad (`usuarios.*`, `roles.*`, `permisos.gestionar`, `bitacora.ver`) + 4 de GestionGlobal (`aulas.*`).

**Asignación (definida en `SeguridadSeeder`):**
- **Administrador:** TODOS los permisos.
- **Coordinador CUP:** todos **menos** la "seguridad pura" (gestión de usuarios/roles/permisos). Conserva `bitacora.ver` + `aulas.*`.
- **Auditor:** solo los de lectura (`*.ver`).
- **Docente / Postulante:** sin permisos administrativos (acceden a su dashboard).

**Cómo se guarda:** tabla pivot `rol_permiso` (rol_id, permiso_id). En código: `$rol->permisos()->sync([...])`.

**Cómo se valida (middleware `ExigirPermiso`):**
```php
// app/Http/Middleware/ExigirPermiso.php
if ($user->rol && $user->rol->nombre === 'Administrador') return $next($request); // bypass
if (! $user->tienePermiso($codigoPermiso)) {
    BitacoraLogger::registrar('ACCESO_DENEGADO', 'Seguridad', '...'); // se audita
    abort(403, 'No tenés permiso para realizar esta acción.');
}
```
Y en la ruta: `->middleware('permiso:usuarios.crear')`.

`User::tienePermiso($codigo)` consulta si el rol del usuario tiene ese permiso (vía la relación con `permisos`).

**Ejemplo — que el Auditor pueda crear usuarios:** en `SeguridadSeeder` agregar `usuarios.crear` al `sync` del rol Auditor (o asignarlo desde la pantalla de Roles) y re-seedear. No hay que tocar el middleware.

---

## 9. SISTEMA DE BITÁCORA — CÓMO FUNCIONA

- **Tabla `bitacora`:** `user_id` (nullable), `accion`, `modulo`, `descripcion`, `ip`, `user_agent`, `created_at`.
- **Quién escribe:** `App\Domain\Bitacora\Services\BitacoraLogger::registrar($accion, $modulo, $descripcion, $userId?, $ip?, $userAgent?)`, llamado desde los UseCases y controllers. **Nunca rompe el flujo**: si falla, loguea el error y sigue.
- **Quién lee:** `BitacoraController` (index + show), **solo lectura** — no existe editar ni borrar (inmutabilidad).
- **Permiso:** `bitacora.ver` (lo tienen Administrador, Coordinador y Auditor).
- **Acciones registradas (códigos reales en el código):**
  - Login/sesión: `LOGIN_OK`, `LOGIN_FAIL`, `LOGIN_INACTIVO`, `LOGOUT_OK`
  - Seguridad: `ACCESO_DENEGADO`
  - Usuarios: `USUARIO_CREADO`, `USUARIO_EDITADO`, `USUARIO_INACTIVADO`, `USUARIO_REACTIVADO`
  - Roles: `ROL_CREADO`, `ROL_EDITADO`, `ROL_INACTIVADO`, `ROL_REACTIVADO`
  - Aulas: `AULA_CREADA`, `AULA_EDITADA`, `AULA_INACTIVADA`, `AULA_REACTIVADA`
  - Materias y otros módulos del compañero: `CREAR`, `EDITAR`, `DESACTIVAR`, `ACTIVAR` (con `modulo` = "Materias", etc.)
  - Contraseña (rama auth): `PASSWORD_RESET_SOLICITADO`, `PASSWORD_CAMBIO_PRIMER_LOGIN`

---

## 10. CASOS DE PRUEBA DE LA DEFENSA — DEMOS

> Credencial admin de pruebas: **admin@cup.uagrm.bo / admin123**.
> Las demos 2 y 3 dependen de la rama `feature/auth-mail-primera-vez` (ver sección 13).

### Demo 1 — Login institucional
1. Mostrar `/login` (branding CUP split-screen).
2. Login fallido → mensaje rojo "Credenciales incorrectas".
3. Login correcto como admin → redirige a `/dashboard/admin`.

### Demo 2 — Cambio obligatorio de contraseña (rama auth)
1. Como admin, crear un usuario nuevo en `/seguridad/usuarios/crear`.
2. Logout → login con ese usuario.
3. Se fuerza la pantalla `/password/cambio-obligatorio` (no deja navegar a otro lado).
4. Probar contraseña sin mayúscula/número → error; con `Coord2026` → cambia y entra.

### Demo 3 — Recuperación de contraseña por email (rama auth)
1. `/login` → "¿Olvidaste tu contraseña?".
2. Ingresar email → llega correo real con diseño UAGRM.
3. Click en el enlace → setear nueva contraseña.

### Demo 4 — Bitácora y auditoría
1. `/bitacora`: lista con KPIs y filtros.
2. Filtrar por `LOGIN_FAIL` → intentos fallidos.
3. Click en un registro → IP, user agent, módulo, descripción.
4. Recalcar: **no hay botón de editar/borrar** (inmutable).

### Demo 5 — Cálculo automático de grupos
1. `/dashboard/coordinador`.
2. Panel "Cálculo automático de grupos": **500 inscritos → ⌈500/80⌉ = 7 grupos**.
3. Explicar la fórmula `CEIL(inscritos / 80)`.

### Demo 6 — Sistema de permisos
1. Login como Postulante (o Auditor) e intentar `/seguridad/usuarios` → **403**.
2. Volver como admin a `/bitacora` → mostrar el evento `ACCESO_DENEGADO`.

### Demo 7 — Inactivación lógica vs física
1. En `/seguridad/usuarios`, inactivar un usuario → badge "Inactivo" (no se borra de la BD).
2. Reactivarlo.
3. Bitácora muestra `USUARIO_INACTIVADO` y `USUARIO_REACTIVADO`.

### Demo 8 — Gestión académica (ajustada al proyecto real)
> El proyecto **no** tiene multiempresa/áreas. Mostrar en su lugar la gestión académica:
1. `/periodos` → periodo activo.
2. `/carreras` → carreras + cupo por periodo.
3. `/materias` → materias con **días y horario** estructurados.
4. `/gestion-global/aulas` → aulas con capacidad.
5. `/postulantes` → 500 postulantes inscritos con sus carreras.

---

## 11. POSIBLES PREGUNTAS DEL JURADO (con respuesta modelo)

1. **¿Por qué Laravel?** Framework MVC maduro, ORM Eloquent, migraciones versionadas, scaffolding de auth (Breeze), validación y middleware integrados → desarrollo rápido y seguro.
2. **¿Qué patrón arquitectónico usan?** MVC de base; en los módulos core de seguridad sumamos una capa de aplicación con **UseCases + Repositories** (estilo Clean Architecture) para separar reglas de negocio del HTTP.
3. **¿Por qué dos estilos (UseCases vs MVC clásico)?** Repartición de trabajo: los módulos de seguridad/auditoría (más críticos) usan UseCases; los CRUD académicos usan MVC directo por simplicidad.
4. **¿Cómo manejan la seguridad/acceso?** Autenticación de Laravel + middleware `ExigirPermiso` que valida permisos por ruta; el Administrador tiene bypass.
5. **¿Cómo funcionan los permisos?** Roles ↔ permisos vía pivot `rol_permiso`; `User::tienePermiso()` consulta la relación; las rutas declaran `permiso:codigo`.
6. **¿Qué pasa si elimino un usuario?** No se borra: es **inactivación lógica** (`activo=false`). Se puede reactivar y queda registrado en bitácora.
7. **¿Pueden mostrar el código que valida la contraseña?** En la rama auth: `CambioPasswordObligatorioController` usa `Password::min(8)->mixedCase()->numbers()` y verifica que sea distinta a la actual con `Hash::check`.
8. **¿Por qué la bitácora no tiene borrar/editar?** Es un registro de auditoría: debe ser **inmutable**. Solo se inserta y se consulta.
9. **¿Cómo se calculan los grupos?** `CEIL(total_inscritos / 80)`: 80 es la capacidad por grupo/aula; se redondea hacia arriba.
10. **¿Cómo se guarda el horario de materias?** En `dias_dictado` (JSON con los días) + `hora_inicio`/`hora_fin`; se muestran formateados con accessors del modelo.
11. **¿Las contraseñas se guardan en texto plano?** No: `casts()` aplica `'password' => 'hashed'` (bcrypt).
12. **¿Cómo evitan el secuestro de sesión?** Tras login correcto se llama `session()->regenerate()`.
13. **¿Qué pasa si un usuario inactivo intenta entrar?** Se autentica, se detecta `activo=false`, se hace logout inmediato y se registra `LOGIN_INACTIVO`.
14. **¿Cómo se asignan postulantes a carreras?** Al inscribirse se crean `inscripcion` + `postulacion_carreras` (prioridad 1 obligatoria, 2 opcional).
15. **¿Por qué PostgreSQL?** Robusto, soporta `JSON`, constraints `CHECK` (ej. `inscripciones.estado`), y buen manejo de concurrencia.
16. **¿Cómo poblaron 500 postulantes?** Con un seeder (`DatosDemoSeeder`) que genera personas/inscripciones/carreras dentro de una transacción.
17. **¿Cómo recupera la contraseña un usuario?** Solicita el enlace, se genera un token con `Password::createToken`, se envía por SMTP (Gmail) con plantilla propia, y al volver setea nueva contraseña.
18. **¿Por qué no revelan si el email existe en "olvidé mi contraseña"?** Por seguridad (evita enumeración de usuarios): siempre se muestra el mismo mensaje.
19. **¿Cómo está organizada la UI?** Un layout maestro `base.blade.php` (Bootstrap 5 + paleta institucional) con componentes reutilizables; todas las vistas lo extienden.
20. **¿Qué falta para la entrega final?** Grupos, pagos (Stripe), notas/evaluación, admisión final y reportes (ver sección 12).

---

## 12. PENDIENTES DE LA ENTREGA FINAL (09/06)

- **CU15 — Pagos** (integración con Stripe).
- **CU16 — Grupos** (asignación automática usando el cálculo `CEIL(inscritos/80)` y aulas).
- **CU17-CU21 — Notas y evaluación** (registro de notas por materia, usando los pesos de exámenes ya modelados).
- **CU22-CU26 — Admisión final** (ranking por cupo de carrera con `cupo_carreras`).
- **CU27 — Reportes**.
- **Higiene técnica:** mergear `feature/auth-mail-primera-vez` y `feature/rediseno-ui` a `main`; **proteger con `auth`/`permiso` las rutas académicas** (ver sección 14).

---

## 13. CONTROL DE VERSIONES (Git)

**Ramas actuales:**
- `main` — base integrada (trabajo de ambos hasta antes del rediseño).
- `feature/rediseno-ui` — rediseño UI completo + Materias con horario + seeder demo + cálculo de grupos. **Pusheada.**
- `feature/auth-mail-primera-vez` — recuperación por email + cambio obligatorio de contraseña. **Commiteada, NO pusheada aún.**
- `feature/seguridad` — rama vieja (ya integrada en main).

**Últimos commits (`feature/rediseno-ui`):**
```
debc1e0  Materias con horario estructurado + seeder demo (500) + calculo de grupos
ec4e360  Rediseno UI/UX completo del sistema CUP
c834143  Merge pull request #4 from ary10220/GRAD
```

**Comandos útiles si el profe pregunta:**
```bash
git branch            # ver ramas
git log --oneline -10 # historial reciente
git status            # estado del working tree
```

> Para que las demos 2 y 3 funcionen en vivo, hay que estar en una rama que tenga
> el feature de auth (o mergearlo a `feature/rediseno-ui` / `main` antes del sábado).

---

## 14. LIMITACIONES CONOCIDAS (leer antes de la defensa)

1. **Rutas académicas sin protección de acceso.** Los `Route::resource` de `periodos`,
   `carreras`, `materias`, `docentes`, `postulantes`, `requisitos` y `documentos` en
   `routes/web.php` **no tienen middleware `auth` ni `permiso`**. Hoy se puede entrar sin
   iniciar sesión. Solo Seguridad, Aulas y Bitácora están protegidas. → Pendiente:
   envolverlas en `Route::middleware('auth')` (y permisos por rol).
2. **Dos features en ramas separadas sin mergear** (`auth-mail-primera-vez` y `rediseno-ui`).
   Para una demo integrada conviene unificarlas en `main` antes del sábado.
3. **Tablas vacías:** `requisitos` y `documentos_postulantes` están en 0; si la demo
   incluye CU11/CU14 conviene cargar algunos datos antes.
4. **Dashboards de Docente/Postulante** muestran KPIs en 0 (los modelos de grupos/notas
   todavía no existen — son de la entrega final).
5. El seeder `DatosDemoSeeder` es **destructivo** (borra y recrea materias, docentes y
   postulantes). No correrlo en una BD con datos reales que se quieran conservar.

---

*Generado como guía de estudio interna. Verificar siempre contra el código actual antes de afirmar algo en la defensa.*
