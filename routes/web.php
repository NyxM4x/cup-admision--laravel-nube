<?php

use App\Http\Controllers\Auth\CambioPasswordObligatorioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\DashboardPostulanteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardAdminController;
// ══════════════════════════════════════════════
// PÁGINA DE INICIO
// ══════════════════════════════════════════════
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// ══════════════════════════════════════════════
// DASHBOARD PRINCIPAL (requiere auth)
// ══════════════════════════════════════════════
Route::middleware('auth')->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Dashboards por rol
Route::middleware('auth')->group(function () {
    Route::get('/dashboard/admin', [DashboardAdminController::class, 'index'])->name('dashboard.admin');
    Route::get('/dashboard/coordinador', fn () => view('dashboards.coordinador'))->name('dashboard.coordinador');
    Route::get('/dashboard/docente',     fn () => view('dashboards.docente'))->name('dashboard.docente');
    Route::get('/dashboard/postulante',  [DashboardPostulanteController::class, 'index'])->name('dashboard.postulante');
    Route::get('/dashboard/auditor',     fn () => view('dashboards.auditor'))->name('dashboard.auditor');
    Route::get('/dashboard/pagos',       [PagoController::class, 'panelAdmin'])->name('dashboard.pagos');
    });

// ══════════════════════════════════════════════
// PERFIL
// ══════════════════════════════════════════════
Route::middleware('auth')->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ══════════════════════════════════════════════
// CAMBIO OBLIGATORIO DE PASSWORD (primer login)
// ══════════════════════════════════════════════
Route::middleware('auth')->group(function () {
    Route::get('/password/cambio-obligatorio',  [CambioPasswordObligatorioController::class, 'show'])
        ->name('password.cambio.form');
    Route::post('/password/cambio-obligatorio', [CambioPasswordObligatorioController::class, 'update'])
        ->name('password.cambio.store');
});

// ══════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════
require __DIR__.'/auth.php';

// ══════════════════════════════════════════════
// CU07-CU16 — MÓDULOS ACADÉMICOS + PAGOS
// ══════════════════════════════════════════════
use App\Http\Controllers\PeriodoController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\RequisitoController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\PostulanteController;
use App\Http\Controllers\DocumentoPostulanteController;

Route::middleware('auth')->group(function () {

    // CU07 — Periodos
    Route::resource('periodos', PeriodoController::class)->except(['destroy']);
    Route::post('periodos/{periodo}/archivar',  [PeriodoController::class, 'archivar'])->name('periodos.archivar');
    Route::post('periodos/{periodo}/reactivar', [PeriodoController::class, 'reactivar'])->name('periodos.reactivar');
    Route::post('periodos/{periodo}/cerrar',    [PeriodoController::class, 'cerrar'])->name('periodos.cerrar');

    // CU08 — Carreras
    Route::resource('carreras', CarreraController::class)->except(['destroy']);
    Route::post('carreras/{carrera}/archivar',  [CarreraController::class, 'archivar'])->name('carreras.archivar');
    Route::post('carreras/{carrera}/reactivar', [CarreraController::class, 'reactivar'])->name('carreras.reactivar');

    // CU09 — Materias
    Route::resource('materias', MateriaController::class)->except(['destroy']);
    Route::post('materias/{materia}/archivar',  [MateriaController::class, 'archivar'])->name('materias.archivar');
    Route::post('materias/{materia}/reactivar', [MateriaController::class, 'reactivar'])->name('materias.reactivar');

    // CU11 — Requisitos
    Route::resource('requisitos', RequisitoController::class)->except(['destroy']);
    Route::post('requisitos/{requisito}/archivar',  [RequisitoController::class, 'archivar'])->name('requisitos.archivar');
    Route::post('requisitos/{requisito}/reactivar', [RequisitoController::class, 'reactivar'])->name('requisitos.reactivar');

    // CU12 — Docentes
    Route::resource('docentes', DocenteController::class);
    Route::post('docentes/{docente}/reactivar', [DocenteController::class, 'reactivar'])->name('docentes.reactivar');

    // CU13 — Postulantes
    Route::post('postulantes/verificar-ci', [PostulanteController::class, 'verificarCI'])->name('postulantes.verificar-ci');
    Route::resource('postulantes', PostulanteController::class);
    Route::post('postulantes/{postulante}/archivar',  [PostulanteController::class, 'archivar'])->name('postulantes.archivar');
    Route::post('postulantes/{postulante}/reactivar', [PostulanteController::class, 'reactivar'])->name('postulantes.reactivar');

    // CU14 — Documentos
    Route::get('documentos',                           [DocumentoPostulanteController::class, 'index'])->name('documentos.index');
    Route::get('documentos/{inscripcion}',             [DocumentoPostulanteController::class, 'show'])->name('documentos.show');
    Route::post('documentos/{inscripcion}/actualizar', [DocumentoPostulanteController::class, 'actualizar'])->name('documentos.actualizar');

    // CU15+CU16 — Pagos y habilitación estudiante
    // CU15+CU16 — Pagos (Stripe + PayPal)
    Route::get('pagos/admin',                         [PagoController::class, 'panelAdmin'])->name('pagos.admin');
    Route::get('pagos/estado/{inscripcion}',          [PagoController::class, 'estadoPago'])->name('pagos.estado');
    Route::get('pagos/seleccionar/{inscripcion}',     [PagoController::class, 'seleccionarMetodo'])->name('pagos.seleccionar');
    Route::get('pagos/stripe/{inscripcion}',          [PagoController::class, 'stripeForm'])->name('pagos.stripe');
    Route::post('pagos/stripe/{inscripcion}/confirm', [PagoController::class, 'stripeConfirmar'])->name('pagos.stripe.confirmar');
    Route::get('pagos/paypal/{inscripcion}/crear',    [PagoController::class, 'paypalCrear'])->name('pagos.paypal.crear');
    Route::get('pagos/paypal/{inscripcion}/exito',    [PagoController::class, 'paypalExito'])->name('pagos.paypal.success');
    Route::get('pagos/paypal/{inscripcion}/cancelar', [PagoController::class, 'paypalCancelar'])->name('pagos.paypal.cancel');
    Route::get('pagos/paypal/retorno',                [PagoController::class, 'paypalRetorno'])->name('pagos.paypal.retorno');
    Route::get('pagos/paypal/cancelar-general',       [PagoController::class, 'paypalCancelarGeneral'])->name('pagos.paypal.cancelar-general');
    Route::post('pagos/{pago}/aprobar',               [PagoController::class, 'aprobar'])->name('pagos.aprobar');
    Route::post('pagos/{pago}/rechazar',              [PagoController::class, 'rechazar'])->name('pagos.rechazar');
    });

// ══════════════════════════════════════════════
// CU03/CU04/CU05 — SEGURIDAD
// ══════════════════════════════════════════════
use App\Http\Controllers\Seguridad\UsuarioController;
use App\Http\Controllers\Seguridad\RolController;
use App\Http\Controllers\Seguridad\PermisoController;

Route::prefix('seguridad')->middleware('auth')->name('')->group(function () {

    // CU03 — Usuarios
    Route::get('/usuarios',                      [UsuarioController::class, 'index'])->middleware('permiso:usuarios.ver')->name('usuarios.index');
    Route::get('/usuarios/crear',                [UsuarioController::class, 'create'])->middleware('permiso:usuarios.crear')->name('usuarios.create');
    Route::post('/usuarios',                     [UsuarioController::class, 'store'])->middleware('permiso:usuarios.crear')->name('usuarios.store');
    Route::get('/usuarios/{usuario}/editar',     [UsuarioController::class, 'edit'])->middleware('permiso:usuarios.editar')->name('usuarios.edit');
    Route::put('/usuarios/{usuario}',            [UsuarioController::class, 'update'])->middleware('permiso:usuarios.editar')->name('usuarios.update');
    Route::delete('/usuarios/{usuario}',         [UsuarioController::class, 'destroy'])->middleware('permiso:usuarios.eliminar')->name('usuarios.destroy');
    Route::post('/usuarios/{usuario}/reactivar', [UsuarioController::class, 'reactivar'])->middleware('permiso:usuarios.editar')->name('usuarios.reactivar');

    // CU04 — Roles
    Route::get('/roles',                   [RolController::class, 'index'])->middleware('permiso:roles.ver')->name('roles.index');
    Route::get('/roles/crear',             [RolController::class, 'create'])->middleware('permiso:roles.crear')->name('roles.create');
    Route::post('/roles',                  [RolController::class, 'store'])->middleware('permiso:roles.crear')->name('roles.store');
    Route::get('/roles/{rol}/editar',      [RolController::class, 'edit'])->middleware('permiso:roles.editar')->name('roles.edit');
    Route::put('/roles/{rol}',             [RolController::class, 'update'])->middleware('permiso:roles.editar')->name('roles.update');
    Route::delete('/roles/{rol}',          [RolController::class, 'destroy'])->middleware('permiso:roles.eliminar')->name('roles.destroy');
    Route::post('/roles/{rol}/reactivar',  [RolController::class, 'reactivar'])->middleware('permiso:roles.editar')->name('roles.reactivar');

    // CU05 — Permisos
    Route::get('/permisos',        [PermisoController::class, 'index'])->middleware('permiso:permisos.gestionar')->name('permisos.index');
    Route::get('/permisos/matriz', [PermisoController::class, 'matriz'])->middleware('permiso:permisos.gestionar')->name('permisos.matriz');
});

// ══════════════════════════════════════════════
// CU10 — AULAS
// ══════════════════════════════════════════════
use App\Http\Controllers\GestionGlobal\AulaController;

Route::prefix('gestion-global')->middleware('auth')->name('')->group(function () {
    Route::get('/aulas',                   [AulaController::class, 'index'])->middleware('permiso:aulas.ver')->name('aulas.index');
    Route::get('/aulas/crear',             [AulaController::class, 'create'])->middleware('permiso:aulas.crear')->name('aulas.create');
    Route::post('/aulas',                  [AulaController::class, 'store'])->middleware('permiso:aulas.crear')->name('aulas.store');
    Route::get('/aulas/{aula}/editar',     [AulaController::class, 'edit'])->middleware('permiso:aulas.editar')->name('aulas.edit');
    Route::put('/aulas/{aula}',            [AulaController::class, 'update'])->middleware('permiso:aulas.editar')->name('aulas.update');
    Route::delete('/aulas/{aula}',         [AulaController::class, 'destroy'])->middleware('permiso:aulas.eliminar')->name('aulas.destroy');
    Route::post('/aulas/{aula}/reactivar', [AulaController::class, 'reactivar'])->middleware('permiso:aulas.editar')->name('aulas.reactivar');
});

// ══════════════════════════════════════════════
// CU06 — BITÁCORA (solo lectura)
// ══════════════════════════════════════════════
use App\Http\Controllers\Bitacora\BitacoraController;

Route::middleware(['auth', 'permiso:bitacora.ver'])->prefix('bitacora')->name('bitacora.')->group(function () {
    Route::get('/',           [BitacoraController::class, 'index'])->name('index');
    Route::get('/{bitacora}', [BitacoraController::class, 'show'])->whereNumber('bitacora')->name('show');
});