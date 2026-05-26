<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
use App\Http\Controllers\PeriodoController;

Route::resource('periodos', PeriodoController::class);

require __DIR__.'/auth.php';

// Dashboards por rol (CU01 redirige aquí según el rol del usuario)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard/admin', fn () => view('dashboards.admin'))->name('dashboard.admin');
    Route::get('/dashboard/coordinador', fn () => view('dashboards.coordinador'))->name('dashboard.coordinador');
    Route::get('/dashboard/docente', fn () => view('dashboards.docente'))->name('dashboard.docente');
    Route::get('/dashboard/postulante', fn () => view('dashboards.postulante'))->name('dashboard.postulante');
    Route::get('/dashboard/auditor', fn () => view('dashboards.auditor'))->name('dashboard.auditor');
});

// CU03/CU04/CU05 - Seguridad (usuarios, roles, permisos)
use App\Http\Controllers\Seguridad\UsuarioController;
use App\Http\Controllers\Seguridad\RolController;
use App\Http\Controllers\Seguridad\PermisoController;

Route::prefix('seguridad')->middleware('auth')->group(function () {
    // CU03 - Usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index'])
        ->middleware('permiso:usuarios.ver')->name('usuarios.index');

    Route::get('/usuarios/crear', [UsuarioController::class, 'create'])
        ->middleware('permiso:usuarios.crear')->name('usuarios.create');

    Route::post('/usuarios', [UsuarioController::class, 'store'])
        ->middleware('permiso:usuarios.crear')->name('usuarios.store');

    Route::get('/usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])
        ->middleware('permiso:usuarios.editar')->name('usuarios.edit');

    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])
        ->middleware('permiso:usuarios.editar')->name('usuarios.update');

    Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])
        ->middleware('permiso:usuarios.eliminar')->name('usuarios.destroy');

    Route::post('/usuarios/{usuario}/reactivar', [UsuarioController::class, 'reactivar'])
        ->middleware('permiso:usuarios.editar')->name('usuarios.reactivar');

    // CU04 - Roles
    Route::get('/roles', [RolController::class, 'index'])
        ->middleware('permiso:roles.ver')->name('roles.index');
    Route::get('/roles/crear', [RolController::class, 'create'])
        ->middleware('permiso:roles.crear')->name('roles.create');
    Route::post('/roles', [RolController::class, 'store'])
        ->middleware('permiso:roles.crear')->name('roles.store');
    Route::get('/roles/{rol}/editar', [RolController::class, 'edit'])
        ->middleware('permiso:roles.editar')->name('roles.edit');
    Route::put('/roles/{rol}', [RolController::class, 'update'])
        ->middleware('permiso:roles.editar')->name('roles.update');
    Route::delete('/roles/{rol}', [RolController::class, 'destroy'])
        ->middleware('permiso:roles.eliminar')->name('roles.destroy');
    Route::post('/roles/{rol}/reactivar', [RolController::class, 'reactivar'])
        ->middleware('permiso:roles.editar')->name('roles.reactivar');

    // CU05 - Permisos (read-only + matriz)
    Route::get('/permisos', [PermisoController::class, 'index'])
        ->middleware('permiso:permisos.gestionar')->name('permisos.index');
    Route::get('/permisos/matriz', [PermisoController::class, 'matriz'])
        ->middleware('permiso:permisos.gestionar')->name('permisos.matriz');
});

// CU10 - Gestionar aulas (GestionGlobal)
use App\Http\Controllers\GestionGlobal\AulaController;

Route::prefix('gestion-global')->middleware('auth')->group(function () {
    Route::get('/aulas', [AulaController::class, 'index'])
        ->middleware('permiso:aulas.ver')->name('aulas.index');
    Route::get('/aulas/crear', [AulaController::class, 'create'])
        ->middleware('permiso:aulas.crear')->name('aulas.create');
    Route::post('/aulas', [AulaController::class, 'store'])
        ->middleware('permiso:aulas.crear')->name('aulas.store');
    Route::get('/aulas/{aula}/editar', [AulaController::class, 'edit'])
        ->middleware('permiso:aulas.editar')->name('aulas.edit');
    Route::put('/aulas/{aula}', [AulaController::class, 'update'])
        ->middleware('permiso:aulas.editar')->name('aulas.update');
    Route::delete('/aulas/{aula}', [AulaController::class, 'destroy'])
        ->middleware('permiso:aulas.eliminar')->name('aulas.destroy');
    Route::post('/aulas/{aula}/reactivar', [AulaController::class, 'reactivar'])
        ->middleware('permiso:aulas.editar')->name('aulas.reactivar');
});

// CU06 - Visualizar bitácora (READ-ONLY, inmutable: solo GET)
use App\Http\Controllers\Bitacora\BitacoraController;

Route::middleware('auth')->prefix('bitacora')->name('bitacora.')->middleware('permiso:bitacora.ver')->group(function () {
    Route::get('/', [BitacoraController::class, 'index'])->name('index');
    Route::get('/{bitacora}', [BitacoraController::class, 'show'])->whereNumber('bitacora')->name('show');
});

use App\Http\Controllers\CarreraController;

Route::resource('carreras', CarreraController::class);

Route::post('carreras/{carrera}/reactivar', [CarreraController::class, 'reactivar'])->name('carreras.reactivar');
use App\Http\Controllers\MateriaController;

Route::resource('materias', MateriaController::class);
Route::post('materias/{materia}/reactivar', [MateriaController::class, 'reactivar'])->name('materias.reactivar');

use App\Http\Controllers\RequisitoController;

Route::resource('requisitos', RequisitoController::class);
Route::post('requisitos/{requisito}/reactivar', [RequisitoController::class, 'reactivar'])->name('requisitos.reactivar');

use App\Http\Controllers\DocenteController;

Route::resource('docentes', DocenteController::class);
Route::post('docentes/{docente}/reactivar', [DocenteController::class, 'reactivar'])->name('docentes.reactivar');
