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
