<?php

use App\Http\Controllers\ConsultaPublicaController;
use App\Http\Controllers\EncomiendaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [ConsultaPublicaController::class, 'form'])->name('consulta.form');
Route::post('/consultar', [ConsultaPublicaController::class, 'buscar'])
    ->middleware('throttle:5,1')
    ->name('consulta.buscar');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('/dashboard', '/encomiendas')->name('dashboard');

    Route::get('/encomiendas', [EncomiendaController::class, 'index'])->name('encomiendas.index');
    Route::get('/encomiendas/registrar', [EncomiendaController::class, 'create'])->name('encomiendas.create');
    Route::post('/encomiendas', [EncomiendaController::class, 'store'])->name('encomiendas.store');
    Route::post('/encomiendas/{encomienda}/reasignar', [EncomiendaController::class, 'reasignar'])
        ->middleware('role:administrativa')
        ->name('encomiendas.reasignar');
    Route::post('/encomiendas/{encomienda}/notificar', [EncomiendaController::class, 'notificar'])->name('encomiendas.notificar');
    Route::post('/encomiendas/{encomienda}/entregar', [EncomiendaController::class, 'entregar'])->name('encomiendas.entregar');
    Route::delete('/encomiendas/{encomienda}', [EncomiendaController::class, 'destroy'])->name('encomiendas.destroy');

    Route::get('/estacion', [EncomiendaController::class, 'estacion'])->name('encomiendas.estacion');

    Route::get('/encomiendas/exportar', [EncomiendaController::class, 'exportar'])->name('encomiendas.exportar');

    Route::get('/ajustes', [EncomiendaController::class, 'ajustes'])->name('encomiendas.ajustes');
    Route::put('/ajustes', [EncomiendaController::class, 'ajustesUpdate'])->name('encomiendas.ajustes.update');

    Route::middleware('role:admin')->prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->name('index');
        Route::get('/crear', [UsuarioController::class, 'create'])->name('create');
        Route::post('/', [UsuarioController::class, 'store'])->name('store');
        Route::get('/{usuario}/editar', [UsuarioController::class, 'edit'])->name('edit');
        Route::put('/{usuario}', [UsuarioController::class, 'update'])->name('update');
        Route::post('/{usuario}/resetear-password', [UsuarioController::class, 'resetPassword'])->name('reset-password');
        Route::delete('/{usuario}', [UsuarioController::class, 'destroy'])->name('destroy');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
