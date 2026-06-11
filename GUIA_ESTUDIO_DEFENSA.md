# 📚 GUÍA DE ESTUDIO PARA DEFENSA — Sistema CUP UAGRM-FICCT
*Generada el 2026-06-07 · Para la defensa del sábado · Revisión real del código en `main` (commit `06e890f`)*

> **Manual del Defensor.** Todo lo de aquí está verificado contra el código y la BD reales (conteos vía `tinker`, rutas vía `route:list`). Lo que no se encontró en el código está en la sección 14 (Avisos).

---

## ⚠️ AVISO: PROBLEMA CONOCIDO RESUELTO

El binario de PHP de Laragon se había perdido, pero **ya fue reinstalado (PHP 8.3.21)**.
- **Ruta:** `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`
- **Verificar:** `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe -v` → debe imprimir `PHP 8.3.21`.
- Como `php` **no está en el PATH**, en los comandos de esta guía reemplazá `php` por la ruta completa, o creá un alias.

---

## 0. CHECKLIST PRE-DEFENSA (LO PRIMERO, SÁBADO 8 AM)

```
[ ] Abrir Laragon → Start All
[ ] Verificar luz VERDE en PostgreSQL (y que php.exe -v responda 8.3.21)
[ ] cd C:\laragon\www\cup-admision-laravel
[ ] git pull origin main                 (si NyxM4x subió algo)
[ ] php artisan migrate                  (por si hay migraciones nuevas)
[ ] php artisan serve
[ ] Abrir http://127.0.0.1:8000
[ ] Login: admin@ficct.uagrm.edu.bo / admin1234
[ ] Probar las 5 demos (ver sección 5.DEMOS)
[ ] (OPCIONAL) Resetear la demo de asignación:
        php artisan db:seed --class=PromediosDemoSeeder
    ↳ vuelve a dejar 142 "aprobados" para mostrar la Pre-asignación desde cero.
[ ] Tener este doc abierto en el celular o impreso
[ ] Respirar profundo, conocés tu sistema 💪
```

> **¡OJO con la Pre-asignación!** La asignación CU24 **ya fue ejecutada** en la base actual (hay 142 `admitido_primera` y 0 `aprobado`). Si querés mostrar el ranking de pre-asignación “en vivo”, corré antes `php artisan db:seed --class=PromediosDemoSeeder` para restaurar los 142 aprobados.

---

## 1. RESUMEN EJECUTIVO

**¿Qué hace el sistema?**
El **Sistema CUP (Curso Preuniversitario) de la FICCT-UAGRM** digitaliza el proceso de admisión preuniversitaria de punta a punta: registra postulantes e inscripciones a carreras, valida su documentación, arma la estructura académica (materias, horarios, grupos, docentes y aulas) y, al final, **asigna la carrera definitiva por mérito y cupo**, publica la lista oficial de admitidos y emite reportes y estadísticas.

Está construido sobre **Laravel 13** + **PostgreSQL**, con seguridad propia (usuarios, roles y permisos por matriz), **bitácora inmutable** de auditoría en cada acción relevante, y dashboards por rol. La UI usa **Bootstrap 5** con paleta institucional CUP y componentes reutilizables (buscador y modal de confirmación).

Cubre el **ciclo completo**: el postulante se inscribe, un coordinador valida su documentación, y al cumplir los requisitos el sistema le **crea el usuario y le envía las credenciales por email**. Luego se generan grupos automáticamente y se asignan carreras según ranking de promedios y cupos.

**Equipo:**
- **Ariany Claure Cota** — seguridad transversal + gestión académica + postulantes/documentos + admisión final + reportes/estadísticas.
- **NyxM4x** (compañero) — pagos + evaluación + ranking/podio + IA.

**Reparto de Casos de Uso:**
- **Ariany:** CU01–CU14 (Ciclo 1) + **CU17, CU18, CU19, CU24, CU25, CU26, CU27** (Ciclo 2). *(También CU20 “asignar aula”, ver Avisos.)*
- **NyxM4x:** CU15, CU16, CU20, CU21, CU22, CU23, CU28.

**Stack tecnológico REAL** (de `composer.json`):
- **Backend:** PHP **8.3.21** + **Laravel 13.11.2** (`laravel/framework ^13.8`), `laravel/tinker`.
- **BD:** PostgreSQL (extensión `unaccent` para búsquedas sin tildes).
- **Frontend:** Blade + Bootstrap 5.3 + Bootstrap Icons (CDN).
- **PDF:** `barryvdh/laravel-dompdf ^3.1` · **Excel:** `maatwebsite/excel ^3.1` · **Gráficos:** Chart.js 4.4 (CDN).
- **Email:** Gmail SMTP.

**Estado al 2026-06-07:** **21 CUs implementados de 28** (CU01–CU14, CU17–CU19, CU24–CU27, + CU20). Faltan los del compañero: CU15, CU16, CU21, CU22, CU23, CU28.

---

## 2. ARQUITECTURA DEL SISTEMA

### 2.1 Diagrama de capas

```
┌─────────────────────────────────────────┐
│  VISTAS Blade (resources/views)         │  92 vistas, Bootstrap 5
└──────────────┬──────────────────────────┘
               ↓   middleware: auth, permiso:, ForzarCambioPassword
┌─────────────────────────────────────────┐
│  CONTROLLERS (app/Http/Controllers)     │  30 controllers
└──────────────┬──────────────────────────┘
               ↓
┌─────────────────────────────────────────┐
│  SERVICES / USE CASES                    │  app/Services + app/Domain
│  (algoritmo de asignación, login, CRUDs) │
└──────────────┬──────────────────────────┘
               ↓
┌─────────────────────────────────────────┐
│  MODELOS Eloquent (app/Models)          │  20 modelos
└──────────────┬──────────────────────────┘
               ↓
┌─────────────────────────────────────────┐
│  POSTGRESQL (Laragon, puerto 5432)      │  base: cup_admision
└─────────────────────────────────────────┘
        ▲
        └── BitacoraLogger → tabla bitacora (INMUTABLE, transversal)
```

### 2.2 Patrones usados (todos reales en el código)

| Patrón | Dónde | Qué hace |
|---|---|---|
| **Service Layer** | `app/Services/AsignacionCarreraService.php` | Algoritmo de asignación de carreras (CU24) |
| **Use Case / Interactor** | `app/Domain/Seguridad/UseCases/AutenticarUsuarioUseCase.php`, `app/Domain/Usuarios/UseCases/*`, `app/Domain/Seguridad/UseCases/*Rol*`, `app/Domain/GestionGlobal/Aulas/UseCases/*` | Encapsulan reglas de negocio sensibles (login, CRUD de usuarios/roles/aulas) |
| **Repository** | `app/Domain/.../Repositories/*` (BitacoraRepository, RolRepository, UsuarioRepository, AulaRepository) | Acceso a datos encapsulado |
| **Mailable** | `app/Mail/HabilitacionPostulanteMail.php`, `app/Mail/RecuperarPasswordMail.php` | Emails |
| **Middleware** | `app/Http/Middleware/ExigirPermiso.php`, `app/Http/Middleware/ForzarCambioPassword.php` | Control de permisos por ruta y cambio de clave obligatorio |
| **Blade Components** | `resources/views/components/buscador-cup.blade.php`, `modal-confirmar.blade.php` | UI reutilizable |
| **Export** | `app/Exports/GenericExport.php` | Generación de Excel genérica |
| **Auditoría transversal** | `app/Domain/Bitacora/Services/BitacoraLogger.php` | Log de cada acción (Observer-like) |
| **Soft-delete lógico** | columna `activo` en casi todas las tablas | Archivar en vez de borrar |

---

## 3. BASE DE DATOS

### 3.1 ¿Dónde vive físicamente?
- **Motor:** PostgreSQL en **Laragon**, puerto **5432**.
- **Base:** `cup_admision` · **Usuario:** `postgres` · **Conexión:** `pgsql` (de `.env`).
- **Config Laravel:** `config/database.php` + `.env` (`DB_CONNECTION=pgsql`, `DB_DATABASE=cup_admision`).
- **DBeaver/pgAdmin:** Host `127.0.0.1`, Puerto `5432`, Base `cup_admision`, Usuario `postgres`, contraseña la del `.env`.
- **Consola:**
  ```
  cd C:\laragon\bin\postgresql\<version>\bin
  psql -U postgres -d cup_admision
  \dt                       -- listar tablas
  SELECT count(*) FROM postulantes;
  ```

### 3.2 Las 22 tablas (conteos REALES verificados el 2026-06-07)

| Tabla | Módulo | Descripción | Registros |
|---|---|---|---|
| `users` | Seguridad | Cuentas de acceso (login, rol, bloqueo) | **6** |
| `roles` | Seguridad | Roles del sistema | **6** |
| `permisos` | Seguridad | Permisos atómicos (`usuarios.ver`, …) | **14** |
| `rol_permiso` | Seguridad | Matriz Rol × Permiso | **26** |
| `bitacora` | Auditoría | Registro inmutable de acciones | **98** (28 acciones distintas) |
| `periodos` | Académico | Periodos del CUP | **1** (activo) |
| `carreras` | Académico | Carreras a postular | **5** |
| `cupo_carreras` | Académico | Cupo por carrera/periodo | **5** |
| `materias` | Académico | Materias (MAT, FIS, COM, ING) | **4** (todas activas) |
| `aulas` | Académico | Aulas físicas | **15** |
| `requisitos` | Académico | Requisitos documentales | **5** |
| `profesiones` | Académico | Profesiones de docentes | **6** |
| `personas` | Núcleo | Datos personales (CI, nombre, correo) | **506** |
| `postulantes` | Inscripción | Postulante (persona + colegio) | **500** |
| `inscripciones` | Inscripción | Inscripción a un periodo | **500** |
| `postulacion_carreras` | Inscripción | Preferencias 1ra/2da | **778** |
| `documentos_postulantes` | Inscripción | Checklist de requisitos | **15** |
| `docentes` | Académico | Docente (persona + profesión) | **6** |
| `horarios` | Académico | Bloques horarios fijos | **6** |
| `grupos` | Académico | Grupos por materia/periodo | **0** |
| `grupo_postulante` | Académico | Alumnos por grupo | **0** |
| `resultados_admision` | Admisión | Promedio, ranking, carrera asignada | **200** |

**Detalle `resultados_admision` (200):** `admitido_primera = 142`, `reprobado = 58`, `aprobado = 0` (la asignación ya se ejecutó).
*(Tablas técnicas de Laravel adicionales: `cache`, `jobs`, `sessions`, `password_reset_tokens`.)*

> Comando para re-verificar conteos:
> ```
> php artisan tinker --execute="foreach(['users','roles','permisos','rol_permiso','bitacora','periodos','carreras','cupo_carreras','materias','aulas','requisitos','profesiones','personas','postulantes','inscripciones','postulacion_carreras','documentos_postulantes','docentes','horarios','grupos','grupo_postulante','resultados_admision'] as \$t){ echo str_pad(\$t,28).' = '.DB::table(\$t)->count().PHP_EOL; }"
> ```

### 3.3 Cómo se crea una tabla nueva
- Crear migración: `php artisan make:migration crear_tabla_x`
- Ubicación: `database/migrations/AAAA_MM_DD_HHMMSS_crear_tabla_x.php`
- **Ejemplo real** (`2026_06_05_223747_create_resultados_admision_table.php`, resumido):
  ```php
  Schema::create('resultados_admision', function (Blueprint $table) {
      $table->id();
      $table->foreignId('postulante_id')->constrained('postulantes')->cascadeOnDelete();
      $table->foreignId('periodo_id')->constrained('periodos')->cascadeOnDelete();
      $table->decimal('promedio_final', 5, 2);
      $table->integer('posicion_ranking_general')->nullable();
      $table->foreignId('carrera_asignada_id')->nullable()->constrained('carreras')->nullOnDelete();
      $table->enum('estado_admision', ['aprobado','reprobado','admitido_primera','admitido_segunda','no_admitido_sin_cupo','lista_espera']);
      $table->timestamp('fecha_asignacion')->nullable();
      $table->text('observacion')->nullable();
      $table->timestamps();
      $table->index('estado_admision');
      $table->unique(['postulante_id', 'periodo_id']);
  });
  ```
- Aplicar: `php artisan migrate` · Revertir última: `php artisan migrate:rollback` · Estado: `php artisan migrate:status`
- **Total de migraciones en el proyecto: 33.**

### 3.4 Cómo se cargan datos demo (6 seeders en `database/seeders/`)
| Seeder | Carga |
|---|---|
| `DatabaseSeeder` | Orquestador: llama a los demás + admin, periodos, carreras, materias, requisitos, profesiones, docentes |
| `SeguridadSeeder` | Roles, permisos y matriz rol_permiso |
| `AulasSeeder` | Aulas |
| `HorariosSeeder` | 6 horarios fijos (M1/M2/T1/T2/N1/N2) |
| `DatosDemoSeeder` | 500 postulantes + inscripciones + preferencias de carrera |
| `PromediosDemoSeeder` | 200 resultados con promedios (para CU24) |

- Todo: `php artisan db:seed` · Uno: `php artisan db:seed --class=PromediosDemoSeeder` · Reset total: `php artisan migrate:fresh --seed` **(CUIDADO: borra TODO).**

### 3.5 Diagrama de relaciones

```
users ── rol_id ──► roles ──< rol_permiso >── permisos

personas ──1:1── postulantes ──< inscripciones ──< postulacion_carreras >── carreras
                                     │                                         │
                                     ├──< documentos_postulantes >── requisitos │
                                     └─ periodo_id ──► periodos ──< cupo_carreras

personas ──1:1── docentes ──< grupos >── materias
                                │ ├──► horarios
                                │ └──► aulas
                                └──< grupo_postulante >── postulantes

postulantes ──< resultados_admision >── carreras (carrera_asignada)
                       └─ periodo_id ──► periodos

bitacora ── transversal: registra TODAS las acciones (inmutable)
```

---

## 4. ESTRUCTURA DEL CÓDIGO

### 4.1 Controllers (`app/Http/Controllers/`) — 30 archivos

| Controller | Métodos clave | CU | URL base |
|---|---|---|---|
| `Auth/AuthenticatedSessionController` | store, destroy | **CU01** | `/login` |
| `Auth/PasswordResetLinkController` + `NewPasswordController` | create, store | **CU02** | `/forgot-password`, `/reset-password` |
| `Auth/CambioPasswordObligatorioController` | show, update | (1er login) | `/password/cambio-obligatorio` |
| `Seguridad/UsuarioController` | index, store, update, destroy, reactivar | **CU03** | `/seguridad/usuarios` |
| `Seguridad/RolController` | index, store, update, destroy, reactivar | **CU04** | `/seguridad/roles` |
| `Seguridad/PermisoController` | index, matriz | **CU05** | `/seguridad/permisos` |
| `Bitacora/BitacoraController` | index, show | **CU06** | `/bitacora` |
| `PeriodoController` | index, store, update, archivar, reactivar, **cerrar** | **CU07** | `/periodos` |
| `CarreraController` | index, store, update, archivar, reactivar | **CU08** | `/carreras` |
| `MateriaController` | index, store, update, archivar, reactivar | **CU09** | `/materias` |
| `GestionGlobal/AulaController` | index, store, update, destroy, reactivar | **CU10** | `/gestion-global/aulas` |
| `RequisitoController` | index, store, update, archivar, reactivar | **CU11** | `/requisitos` |
| `DocenteController` | index, store, update, destroy, reactivar | **CU12** | `/docentes` |
| `PostulanteController` | index, store, **verificarCI**, archivar, reactivar | **CU13** | `/postulantes` |
| `DocumentoPostulanteController` | index, show, **actualizar** | **CU14** | `/documentos` |
| `HorarioController` | index, store, update, archivar, reactivar | **CU19** | `/horarios` |
| `GrupoController` | index, store, update, **generarAutomaticos, asignarDocente, asignarAula** | **CU17/18/20** | `/grupos` |
| `AsignacionCarreraController` | vistaPreasignacion, **ejecutar**, vistaResultados | **CU24** | `/admision/...` |
| `ListaAdmitidosController` | index, **publicar, exportar** | **CU25** | `/admision/admitidos` |
| `ReporteController` | index, **pdf, excel, html** | **CU26** | `/reportes` |
| `EstadisticaController` | dashboard, porDocente, porGrupo | **CU27** | `/estadisticas` |
| `ProfileController` | edit, update, destroy | (perfil) | `/profile` |

### 4.2 Models (`app/Models/`) — 20 archivos

| Modelo | Tabla | Relaciones / casts |
|---|---|---|
| `User` | users | `belongsTo Rol`; `tienePermiso()`; casts password=hashed, activo, debe_cambiar_password |
| `Rol` | roles | `belongsToMany Permiso`; `hasMany User` |
| `Permiso` | permisos | `belongsToMany Rol` |
| `Bitacora` | bitacora | `belongsTo User`; **INMUTABLE** (update/delete lanzan excepción); sin updated_at |
| `Periodo` | periodos | casts de fechas + activo |
| `Carrera` | carreras | `hasMany CupoCarrera`; `cupoActivo()` hasOne |
| `CupoCarrera` | cupo_carreras | `belongsTo Carrera`, `belongsTo Periodo` |
| `Materia` | materias | casts activo, dias_dictado=array |
| `Aula` | aulas | scope `activos`; capacidad=int |
| `Requisito` | requisitos | (CRUD por periodo) |
| `Profesion` | profesiones | (catálogo docentes) |
| `Persona` | personas | base de postulantes y docentes |
| `Postulante` | postulantes | `belongsTo Persona`; `hasMany Inscripcion`; `inscripcionActiva()`; activo |
| `Inscripcion` | inscripciones | `belongsTo Postulante/Periodo`; `hasMany PostulacionCarrera/DocumentoPostulante` |
| `PostulacionCarrera` | postulacion_carreras | `belongsTo Inscripcion/Carrera`; `prioridad` |
| `DocumentoPostulante` | documentos_postulantes | `belongsTo Inscripcion/Requisito`; cumplido=bool |
| `Docente` | docentes | `belongsTo Persona/Profesion`; activo |
| `Horario` | horarios | `hasMany Grupo`; accessor `rango` |
| `Grupo` | grupos | `belongsTo` Periodo/Materia/Horario/Aula/Docente; `belongsToMany Postulante` |
| `ResultadoAdmision` | resultados_admision | `belongsTo Postulante/Periodo`; `carreraAsignada()`; promedio_final=decimal:2 |

### 4.3 Vistas Blade (`resources/views/`)
- **`layouts/`** → `base.blade.php` (navbar, paleta CUP, Bootstrap, modal global).
- **`components/`** → `buscador-cup.blade.php`, `modal-confirmar.blade.php` (+ componentes de Breeze).
- **`emails/`** → `habilitacion-postulante.blade.php`, `recuperar-password.blade.php`.
- **Por módulo:** `auth/`, `dashboards/`, `seguridad/`, `bitacora/`, `periodos/`, `carreras/`, `materias/`, `requisitos/`, `gestion-global/` (aulas), `docentes/`, `postulantes/`, `documentos/`, `horarios/`, `grupos/`, `admision/` (+ `admision/pdf/`), `reportes/` (+ `reportes/pdf/`), `estadisticas/`, `profile/`.

### 4.4 Rutas (`routes/web.php`) — 136 rutas
- **Públicas:** `/` (redirige a login o dashboard según sesión), `/login`, `/forgot-password`, `/reset-password`.
- **Autenticadas** (`Route::middleware('auth')`): todo el resto. Las de seguridad agregan `->middleware('permiso:codigo')`.
- Agrupadas por módulo: dashboards, perfil, cambio de password, seguridad (usuarios/roles/permisos), académico (periodos/carreras/materias/horarios/grupos/requisitos), aulas, docentes, postulantes, documentos, admisión, reportes, estadísticas, bitácora.
- Ver todas: `php artisan route:list --except-vendor`.

---

## 5. LOS CASOS DE USO IMPLEMENTADOS (uno por uno)

> Formato: qué hace · actor · URL · controller@método · modelo · vista · bitácora · validaciones · cómo probarlo.

### CU01 — Iniciar sesión
- **Qué hace:** autentica y redirige al dashboard según rol. **Actor:** todos.
- **URL:** `/login` · **Controller:** `Auth/AuthenticatedSessionController@store` → `AutenticarUsuarioUseCase@ejecutar`.
- **Modelo:** `User`. **Vista:** `auth/login.blade.php`.
- **Bitácora:** `LOGIN_OK`, `LOGIN_FAIL`, `LOGIN_LOCKED`, `LOGIN_BLOQUEADO`, `LOGIN_INACTIVO`.
- **Validaciones/reglas:** usuario activo; **bloqueo a los 3 intentos** por 15 min; `session()->regenerate()`.
- **Demo:** entrar a `/` → redirige a `/login` → `admin@ficct.uagrm.edu.bo` / `admin1234`.

### CU02 — Recuperar contraseña + cambio obligatorio (1er login)
- **URL:** `/forgot-password`, `/reset-password`, `/password/cambio-obligatorio`.
- **Controllers:** `Auth/PasswordResetLinkController`, `Auth/NewPasswordController`, `Auth/CambioPasswordObligatorioController`.
- **Mailable:** `RecuperarPasswordMail` · **Vista email:** `emails/recuperar-password.blade.php`.
- **Middleware:** `ForzarCambioPassword` obliga a cambiar la clave temporal antes de usar el sistema.

### CU03 — Usuarios · CU04 — Roles · CU05 — Permisos
- **URLs:** `/seguridad/usuarios`, `/seguridad/roles`, `/seguridad/permisos`.
- **Controllers:** `Seguridad/UsuarioController`, `RolController`, `PermisoController` (con UseCases en `app/Domain/Usuarios` y `app/Domain/Seguridad`).
- **Reglas:** inactivación lógica; rol **Administrador** no se inactiva; matriz de permisos editable. **Permisos:** `usuarios.*`, `roles.*`, `permisos.gestionar`.
- **Bitácora:** acciones de creación/edición/inactivación de usuarios y roles.

### CU06 — Bitácora (solo lectura)
- **URL:** `/bitacora` · **Controller:** `Bitacora/BitacoraController` · **Permiso:** `bitacora.ver`.
- **Regla:** tabla **inmutable** (el modelo prohíbe update/delete). **Demo:** mostrar el listado filtrable de acciones.

### CU07 — Periodos
- **URL:** `/periodos` · **Controller:** `PeriodoController`.
- **Reglas:** un solo periodo activo; **archivar/reactivar**; **cerrar** (inactiva periodo + postulantes, conserva inscripciones).
- **Bitácora:** `PERIODO_ARCHIVADO`, `PERIODO_REACTIVADO`, `PERIODO_CERRADO`.

### CU08 — Carreras · CU09 — Materias · CU11 — Requisitos
- **URLs:** `/carreras`, `/materias`, `/requisitos`.
- **Reglas:** buscador sin tildes (`unaccent`), filtro Activos/Inactivos/Todos, **archivar/reactivar** (sin borrado físico: `destroy` excluido del resource).
- **Bitácora:** `CARRERA_ARCHIVADA/REACTIVADA`, `MATERIA_ARCHIVADA/REACTIVADA`, `REQUISITO_ARCHIVADO/REACTIVADO`.

### CU10 — Aulas
- **URL:** `/gestion-global/aulas` · **Controller:** `GestionGlobal/AulaController` (+ UseCases `app/Domain/GestionGlobal/Aulas/`). Filtros + inactivar/reactivar.

### CU12 — Docentes
- **URL:** `/docentes` · **Controller:** `DocenteController`. Buscador (nombre/CI/profesión), inactivar/reactivar.

### CU13 — Registrar postulante (+ reinscripción)
- **Qué hace:** crea persona + postulante + inscripción + preferencias. Si el **CI ya existe**, propone **reinscripción** (reusa persona, nueva inscripción, reactiva si estaba archivado).
- **URL:** `/postulantes`, `/postulantes/create` · **Controller:** `PostulanteController@store` y `@verificarCI` (AJAX).
- **Validaciones:** CI/nombre/colegio/carrera_1 requeridos; sin duplicar inscripción en el periodo activo.
- **Bitácora:** `POSTULANTE_CREADO`, `POSTULANTE_REINSCRITO`, `POSTULANTE_ARCHIVADO/REACTIVADO`.
- **Demo:** en `/postulantes/create` escribir CI `8000001` y salir del campo → modal “Persona ya registrada”.

### CU14 — Documentos / Habilitación
- **Qué hace:** checklist de requisitos; al cumplir **todos los obligatorios**, crea el usuario del postulante y le **envía credenciales por email**.
- **URL:** `/documentos`, `/documentos/{inscripcion}` · **Controller:** `DocumentoPostulanteController@actualizar` → `habilitarPostulante`.
- **Reglas:** habilita solo si todos los obligatorios están tildados; **idempotente** (no reenvía email si el usuario ya existe).
- **Mailable:** `HabilitacionPostulanteMail` · **Vista email:** `emails/habilitacion-postulante.blade.php` · **Bitácora:** `POSTULANTE_HABILITADO`.

### CU17 — Generar grupos (automático)
- **URL:** `/grupos/generar-automaticos` · **Controller:** `GrupoController@generarAutomaticos`.
- **Regla:** crea `CEIL(habilitados / 70)` grupos por materia activa; cupo 70; idempotente. **Bitácora:** `GRUPOS_GENERADOS_AUTO`.

### CU18 — Asignar docente
- **URL (POST):** `/grupos/{grupo}/asignar-docente` · **Regla:** máximo 4 grupos por docente/periodo. **Bitácora:** `DOCENTE_ASIGNADO`.

### CU19 — Horarios
- **URL:** `/horarios` · **Controller:** `HorarioController`. 6 horarios fijos; CRUD; valida `hora_fin > hora_inicio`. **Bitácora:** `HORARIO_CREADO/EDITADO/ARCHIVADO/REACTIVADO`.

### CU20 — Asignar aula *(implementado, ver Avisos)*
- **URL (POST):** `/grupos/{grupo}/asignar-aula` · **Reglas:** `cupo_max ≤ capacidad`; sin choque aula+horario. **Bitácora:** `AULA_ASIGNADA`.

### CU24 — Asignar carrera definitiva
- **URL:** `/admision/preasignacion`, `/admision/ejecutar`, `/admision/resultados`.
- **Controller:** `AsignacionCarreraController` · **Service:** `AsignacionCarreraService@ejecutarAsignacion` (transaccional).
- **Modelo:** `ResultadoAdmision`. **Bitácora:** `ASIGNACION_EJECUTADA`.

### CU25 — Lista final de admitidos
- **URL:** `/admision/admitidos` (+ `publicar`, `exportar`) · **Controller:** `ListaAdmitidosController`.
- Filtros periodo/estado/carrera + búsqueda; **Publicar** (marca `periodos.lista_publicada` + fecha); **Exportar PDF**. **Bitácora:** `LISTA_PUBLICADA`.

### CU26 — Reportes
- **URL:** `/reportes`, `/reportes/{tipo}/{pdf|excel|html}` · **Controller:** `ReporteController`.
- 5 reportes (inscritos, aprobados_reprobados, admitidos_por_carrera, promedios_por_materia, bitácora) × 3 formatos.

### CU27 — Estadísticas
- **URL:** `/estadisticas`, `/estadisticas/docentes`, `/estadisticas/grupos` · **Controller:** `EstadisticaController`.
- Gráficos Chart.js (torta por carrera, barras aprob/reprob, barras promedio por materia) + tablas por docente/grupo.

### 5.DEMOS — Las 5 demos para el jurado
1. **Login + redirect:** `/` → login → dashboard.
2. **Postulante/reinscripción:** `/postulantes/create` → CI `8000001` → modal.
3. **Grupos:** `/grupos` → “Generar automáticos” → 4 materias × CEIL(habilitados/70).
4. **Admisión:** `/admision/resultados` (142 admitidos) → `/admision/admitidos` → Exportar PDF.
5. **Reportes + Estadísticas:** `/reportes` (bajar PDF/Excel) → `/estadisticas` (3 gráficos).

---

## 6. SEGURIDAD Y AUTENTICACIÓN

### 6.1 Login (CU01)
- **Controller real:** `app/Http/Controllers/Auth/AuthenticatedSessionController.php` → lógica en `app/Domain/Seguridad/UseCases/AutenticarUsuarioUseCase.php`.
- Credenciales con `Auth::attempt()`; password **bcrypt** (`password => hashed`).
- `session()->regenerate()` tras login (anti session-fixation).
- Bloqueo: 3 intentos fallidos → `bloqueado_hasta = now()+15min`.

### 6.2 Roles y permisos
- Matriz `rol_permiso` (26 asignaciones, 14 permisos, 6 roles).
- Aplicar permiso en una ruta: `->middleware('permiso:usuarios.ver')` (middleware `ExigirPermiso`).
- En código: `Auth::user()->tienePermiso('roles.crear')`.
- El rol **Administrador** se trata como super-rol y no puede inactivarse.

### 6.3 Bitácora inmutable
- **Tabla `bitacora`** read-only por diseño: el modelo `Bitacora` lanza excepción si se intenta `update`/`delete`.
- **Cómo se loguea** (código real, `BitacoraLogger`):
  ```php
  \App\Domain\Bitacora\Services\BitacoraLogger::registrar('LOGIN_OK','Seguridad','Sesión iniciada: '.$user->email, $user->id);
  ```
  Captura solo `user_id`, `ip` y `user_agent`. **Nunca rompe el flujo** (try/catch interno).
- **98 registros, 28 acciones distintas** hoy: LOGIN_OK, LOGIN_FAIL, POSTULANTE_HABILITADO, GRUPOS_GENERADOS_AUTO, ASIGNACION_EJECUTADA, etc.

---

## 7. FUNCIONALIDADES ESPECIALES

### 7.1 Email (Gmail SMTP)
- `.env`: `MAIL_MAILER=smtp`, `MAIL_HOST=smtp.gmail.com`, `MAIL_PORT=587`, `MAIL_ENCRYPTION=tls`, `MAIL_FROM_ADDRESS=claurearia@gmail.com`.
- Mailables: `HabilitacionPostulanteMail`, `RecuperarPasswordMail`. Plantillas: `resources/views/emails/`.
- Probar: `php artisan tinker` →
  ```php
  Mail::to('test@test.com')->send(new \App\Mail\HabilitacionPostulanteMail('Juan','test@test.com','Temp123','Informática'));
  ```
- ⚠️ Para la demo: `MAIL_MAILER=log` escribe el correo en `storage/logs/laravel.log` (no lo manda) → evita rebotes a Gmail.

### 7.2 Reportes (CU26)
- Librerías: `barryvdh/laravel-dompdf` (PDF) + `maatwebsite/excel` (Excel).
- Plantillas PDF: `resources/views/reportes/pdf/generico.blade.php` y `resources/views/admision/pdf/lista-admitidos.blade.php`.
- 5 reportes × 3 formatos. Export Excel genérico: `app/Exports/GenericExport.php`.

### 7.3 Gráficos (CU27)
- **Chart.js 4.4** (CDN) en `resources/views/estadisticas/dashboard.blade.php`.
- Cambiar tipo/data: editar `type: 'pie'|'bar'|'line'` y los `@json(...)` del `<script>`.

### 7.4 Algoritmo de asignación (CU24)
- **Archivo:** `app/Services/AsignacionCarreraService.php` → `ejecutarAsignacion(Periodo $periodo)`.
- **Cascada (paso a paso):**
  1. Traer aprobados del periodo **ordenados por `promedio_final` DESC**.
  2. Cargar cupos (`cupo_carreras`).
  3. Por cada aprobado (en orden de ranking): tomar sus preferencias (`postulacion_carreras` por prioridad).
  4. ¿Cupo en la **1ra**? → `admitido_primera`. Si no, ¿**2da**? → `admitido_segunda`. Si no → `no_admitido_sin_cupo`.
  5. Guardar ranking, carrera asignada, estado y fecha; actualizar `postulantes.estado`.
  6. Todo dentro de una **transacción** (atomicidad).
- **Código resumido:**
  ```php
  $aprobados = ResultadoAdmision::where('periodo_id',$p->id)
      ->where('estado_admision','aprobado')->orderByDesc('promedio_final')->get();
  $cupos = CupoCarrera::where('periodo_id',$p->id)->pluck('cupo_max','carrera_id');
  DB::transaction(function () use (...) {
      foreach ($aprobados as $r) {
          $r->posicion_ranking_general = $pos++;
          foreach ($preferencias as $pref) {
              if (($usado[$pref->carrera_id] ?? 0) < $cupos[$pref->carrera_id]) {
                  $r->carrera_asignada_id = $pref->carrera_id;
                  $r->estado_admision = $pref->prioridad==1 ? 'admitido_primera' : 'admitido_segunda';
                  // ...cuenta y rompe
              }
          }
          if (!$asignada) $r->estado_admision = 'no_admitido_sin_cupo';
          $r->save();
      }
  });
  ```
- **Probar:** Admisión → Pre-asignación → “Ejecutar asignación”.

---

## 8. COMANDOS ÚTILES (CHEAT SHEET)

> Prefijo: `& "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe"` antes de `artisan` (PHP no está en PATH).

### 8.1 Servidor
| Comando | Para qué |
|---|---|
| `php artisan serve` | Levantar servidor en `:8000` |
| `Ctrl+C` | Apagar el servidor |

### 8.2 Migraciones
| Comando | Para qué |
|---|---|
| `php artisan make:migration X` | Crear migración |
| `php artisan migrate` | Aplicar pendientes |
| `php artisan migrate:rollback` | Revertir la última |
| `php artisan migrate:status` | Ver estado |
| `php artisan migrate:fresh --seed` | Resetear todo + seeders (BORRA TODO) |

### 8.3 Caché
| Comando | Para qué |
|---|---|
| `php artisan view:clear` | Limpiar vistas compiladas |
| `php artisan route:clear` | Limpiar rutas |
| `php artisan config:clear` | Limpiar config |

### 8.4 Git
| Comando | Para qué |
|---|---|
| `git status` | Ver cambios |
| `git log --oneline -10` | Últimos commits |
| `git pull origin main` | Bajar cambios |
| `git push origin main` | Subir cambios |

---

## 9. ESCENARIOS DE MODIFICACIÓN EN VIVO

### 9.1 “Agreguen un campo a Postulantes”
1. `php artisan make:migration add_campo_to_postulantes_table --table=postulantes`
2. En la migración: `$table->string('campo')->nullable();`
3. `php artisan migrate`
4. Agregar `'campo'` a `$fillable` en `app/Models/Postulante.php`
5. Input en `resources/views/postulantes/create.blade.php` y `edit.blade.php`
6. Validar en `PostulanteController::store/update` y mostrar la columna en `index.blade.php`

### 9.2 “Cambien el cupo máximo de grupos”
- Archivo: `app/Http/Controllers/GrupoController.php` · constante `CUPO_DEFAULT` (= **70**) y `ceil($habilitados / 70)` en `generarAutomaticos()`.

### 9.3 “Agreguen un reporte nuevo”
1. Agregar tipo al array `ReporteController::REPORTES`
2. Agregar su `case` en `ReporteController::construir()` (encabezados + filas)
3. (PDF/Excel/HTML salen del template genérico; la fila aparece sola en `reportes/index.blade.php`)

### 9.4 “Agreguen una validación”
- Input → reglas en `$request->validate([...])` del Controller.
- Regla de negocio → en el Service o el método del Controller.
- BD → `CHECK`/`unique` en una migración.

---

## 10. PREGUNTAS FRECUENTES (FAQ del jurado)

**P1 — ¿Por qué Laravel y no Django/Spring?** Es el framework PHP más usado localmente, con ORM (Eloquent), migraciones, auth y Blade listos; aceleró los CRUDs y la seguridad sin reinventar.

**P2 — ¿Por qué PostgreSQL y no MySQL?** Mejor integridad referencial, `enum`/constraints ricos y la extensión `unaccent` para búsquedas sin tildes.

**P3 — ¿Qué arquitectura usaron?** MVC de Laravel + capa de dominio (UseCases/Repositories) para seguridad + un Service para el algoritmo de asignación. Clean Architecture pragmática.

**P4 — ¿Cómo manejan la seguridad?** Auth de Laravel (bcrypt, sesiones), roles/permisos por matriz, middleware `permiso:`, bloqueo por intentos y bitácora.

**P5 — ¿Cómo funciona la bitácora?** `BitacoraLogger::registrar()` desde cada CU; guarda acción/módulo/descripción/usuario/IP/user-agent. La tabla es inmutable.

**P6 — ¿Qué pasa si se cae el servidor?** Los datos viven en PostgreSQL; al reiniciar (`php artisan serve`) el estado se recupera íntegro.

**P7 — ¿Por qué Bootstrap y no Tailwind?** Componentes listos y menor curva para el equipo; suficiente para un panel admin. Igual definimos paleta CUP propia con variables CSS.

**P8 — ¿Cómo escalarían a 5000 postulantes?** Índices ya presentes (`estado_admision`, `unique`s), listas paginadas, y para más escala: colas (jobs) para emails/reportes y caché de estadísticas.

**P9 — ¿Tests automatizados?** Aún no (PHPUnit); la verificación fue manual + pruebas en `tinker` con transacciones revertidas. Es lo principal a sumar.

**P10 — ¿Qué falta para producción?** Tests, email transaccional, HTTPS, secrets seguros, y completar los CUs del compañero.

**P11 — ¿Cómo manejaron Git en equipo?** `main` + rama por feature, merge `--no-ff`, mensajes descriptivos (ver `git log`).

**P12 — ¿Por qué soft-delete y no hard-delete?** Integridad e historial: archivar (`activo=false`) preserva periodos/inscripciones/resultados. Por eso `destroy` se excluyó de los resources académicos.

**P13 — ¿Cómo evitan inyección SQL?** Eloquent/Query Builder usan **prepared statements**; incluso los `whereRaw('unaccent(...) ilike unaccent(?)', [$q])` usan bindings.

**P14 — ¿Qué hace `unaccent`?** Normaliza tildes en Postgres para buscar “Garcia” y encontrar “García”. Se habilita en una migración.

**P15 — ¿Qué patrones usaron?** Service, Use Case, Repository, Mailable, Middleware, Blade Components y auditoría transversal.

**P16 — ¿Cómo funciona el algoritmo de asignación?** Ordena aprobados por promedio DESC y asigna en cascada 1ra → 2da → sin cupo, respetando `cupo_carreras`, todo en una transacción (ver sección 7.4).

**P17 — ¿Cómo se generan los grupos?** `CEIL(habilitados/70)` grupos por materia activa, idempotente; valida docente ≤ 4 grupos y aula sin choque de horario.

**P18 — ¿Cómo se hace la habilitación automática?** Al tildar todos los requisitos obligatorios de una inscripción, se crea el usuario y se envía el email con credenciales (idempotente).

**P19 — ¿Pueden mostrar la bitácora?** Sí, en `/bitacora` (solo lectura). Hoy hay 98 registros / 28 acciones distintas.

**P20 — ¿Cómo aseguran que los datos no se pierdan?** Persisten en PostgreSQL con FKs y `unique`s; soft-delete para no perder histórico; commits versionados en Git.

---

## 11. PUNTOS DÉBILES (HONESTOS) Y CÓMO RESPONDER

### 11.1 No implementado (es de NyxM4x)
- **CU15** Pagos · **CU16** Habilitación post-pago · **CU21** Notas de exámenes · **CU22** Nota final · **CU23** Podio/ranking · **CU28** Consultas IA (OpenAI).
- **CU20** (asignar aulas): **ya implementado** de nuestro lado (`GrupoController::asignarAula`), aunque en el reparto figura del compañero.

### 11.2 Incompleto en tus CUs (etiquetado en la UI)
- **Promedios por materia (CU26/CU27):** son **demo/referenciales** porque las notas por materia dependen del CU21.
- **Estadísticas por docente/grupo (CU27):** estructura real (nº grupos, ocupación); promedio/% es referencial hasta tener notas (CU22). El dashboard lo avisa con banner amarillo.

### 11.3 Respuesta modelo
> “Esa funcionalidad la implementa mi compañero NyxM4x; está modelada como CU[XX] en el diagrama de casos de uso y se entrega en el Ciclo 2 final el 09/06. Nuestra integración deja el punto de conexión listo (p. ej., la tabla `resultados_admision` que consume CU24).”

---

## 12. TOP 10 ARCHIVOS QUE MEMORIZAR

| # | Archivo | Para qué |
|---|---|---|
| 1 | `.env` | Config (BD `cup_admision`, email, claves) |
| 2 | `routes/web.php` | Las 136 rutas |
| 3 | `app/Domain/Seguridad/UseCases/AutenticarUsuarioUseCase.php` | Lógica real de login (CU01) |
| 4 | `app/Http/Controllers/PostulanteController.php` | Postulantes + reinscripción (CU13) |
| 5 | `app/Domain/Bitacora/Services/BitacoraLogger.php` + `app/Models/Bitacora.php` | Auditoría inmutable |
| 6 | `resources/views/layouts/base.blade.php` | Layout + navbar |
| 7 | `database/seeders/DatabaseSeeder.php` | Orquestador de datos demo |
| 8 | `app/Services/AsignacionCarreraService.php` | Algoritmo de asignación (CU24) |
| 9 | `config/database.php` | Conexión PostgreSQL |
| 10 | `app/Mail/HabilitacionPostulanteMail.php` | Email de habilitación (CU14) |

> ⚠️ El login NO está en un `AuthController.php` (no existe): vive en `Auth/AuthenticatedSessionController` + `AutenticarUsuarioUseCase`.

---

## 13. ESTRUCTURA COMPLETA DE CARPETAS

```
C:\laragon\www\cup-admision-laravel\
├── app\
│   ├── Http\Controllers\        ← 30 controllers (Auth\, Seguridad\, Bitacora\, GestionGlobal\, …)
│   ├── Http\Middleware\         ← ExigirPermiso.php, ForzarCambioPassword.php
│   ├── Models\                  ← 20 modelos Eloquent
│   ├── Mail\                    ← HabilitacionPostulanteMail, RecuperarPasswordMail
│   ├── Services\                ← AsignacionCarreraService.php
│   ├── Exports\                 ← GenericExport.php
│   └── Domain\                  ← Bitacora\, Seguridad\, Usuarios\, GestionGlobal\ (UseCases + Repositories)
├── database\
│   ├── migrations\              ← 33 migraciones
│   ├── seeders\                 ← 6 seeders
│   └── factories\
├── resources\views\
│   ├── layouts\                 ← base.blade.php
│   ├── components\              ← buscador-cup, modal-confirmar
│   ├── emails\                  ← habilitacion-postulante, recuperar-password
│   └── [módulos]\               ← postulantes, documentos, periodos, carreras, materias,
│                                    horarios, grupos, admision (+pdf), reportes (+pdf),
│                                    estadisticas, seguridad, gestion-global, docentes,
│                                    bitacora, dashboards, auth, profile
├── routes\
│   └── web.php                  ← 136 rutas
├── config\                      ← database.php, mail.php, …
├── public\
├── .env                         ← credenciales (BD, email)
└── composer.json                ← laravel/framework ^13.8, dompdf ^3.1, excel ^3.1
```

---

## 14. AVISOS PENDIENTES / OBSERVACIONES

1. **PHP reinstalado (8.3.21)** en `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\`. `php` no está en PATH → usar la ruta completa. Verificar con `php.exe -v` antes de la demo.
2. **La asignación CU24 ya está ejecutada** en la BD (142 `admitido_primera`, 58 `reprobado`, 0 `aprobado`). Por eso **`/admision/preasignacion` muestra 0 aprobados ahora**. Para volver a demostrar la pre-asignación desde cero: `php artisan db:seed --class=PromediosDemoSeeder`.
3. **`grupos = 0`**: no hay grupos en la BD; generalos en la demo (`/grupos` → “Generar automáticos”) → con 4 materias activas y ~499 habilitados serán `CEIL(499/70)=8 × 4 = 32` grupos.
4. **CU20 (asignar aula)** está implementado en `GrupoController::asignarAula` aunque el reparto lo asigna al compañero — aclararlo si preguntan.
5. **Email a Gmail real:** `MAIL_FROM_ADDRESS=claurearia@gmail.com`. Para la demo conviene `MAIL_MAILER=log` para no mandar correos reales (rebotan, porque los postulantes demo usan `@test.bo`).
6. **Sin tests automatizados** (PHPUnit) todavía; verificación fue manual + tinker.
7. **Promedios por materia / stats por grupo** son referenciales hasta que el compañero cargue notas (CU21–CU22).
8. **Todas las migraciones están aplicadas** (33). No se detectaron migraciones pendientes ni rutas rotas en la última revisión.

---

*Generado para la defensa del Sistema CUP — UAGRM · FICCT. Documento de estudio local (NO versionado en Git).*
