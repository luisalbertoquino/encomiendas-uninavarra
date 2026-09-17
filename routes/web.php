<?php

use App\Http\Controllers\BusquedaPersonaController;
use App\Http\Controllers\ColaboradorController;
use App\Http\Controllers\ConsultaPublicaController;
use App\Http\Controllers\DependenciaController;
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
    Route::get('/dashboard', function () {
        return redirect(auth()->user()->role === 'recepcion'
            ? route('encomiendas.create')
            : route('encomiendas.index'));
    })->name('dashboard');

    Route::middleware('role:administrativa,admin')->group(function () {
        Route::get('/encomiendas', [EncomiendaController::class, 'index'])->name('encomiendas.index');
        Route::post('/encomiendas/{encomienda}/reasignar', [EncomiendaController::class, 'reasignar'])->name('encomiendas.reasignar');
        Route::post('/encomiendas/{encomienda}/notificar', [EncomiendaController::class, 'notificar'])->name('encomiendas.notificar');
        Route::post('/encomiendas/{encomienda}/entregar', [EncomiendaController::class, 'entregar'])->name('encomiendas.entregar');
        Route::delete('/encomiendas/{encomienda}', [EncomiendaController::class, 'destroy'])->name('encomiendas.destroy');
        Route::get('/encomiendas/exportar', [EncomiendaController::class, 'exportar'])->name('encomiendas.exportar');
        Route::get('/ajustes', [EncomiendaController::class, 'ajustes'])->name('encomiendas.ajustes');
        Route::put('/ajustes', [EncomiendaController::class, 'ajustesUpdate'])->name('encomiendas.ajustes.update');
        Route::resource('dependencias', DependenciaController::class)->except('show')->parameters(['dependencias' => 'dependencia']);
    });

    Route::middleware('role:recepcion,admin')->group(function () {
        Route::get('/encomiendas/registrar', [EncomiendaController::class, 'create'])->name('encomiendas.create');
        Route::post('/encomiendas', [EncomiendaController::class, 'store'])->name('encomiendas.store');
    });

    Route::get('/estacion', [EncomiendaController::class, 'estacion'])->name('encomiendas.estacion');

    Route::get('/buscar-personas/{tipo}', [BusquedaPersonaController::class, 'buscar'])->name('personas.buscar');

    Route::middleware('role:admin')->group(function () {
        Route::prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/', [UsuarioController::class, 'index'])->name('index');
            Route::get('/crear', [UsuarioController::class, 'create'])->name('create');
            Route::post('/', [UsuarioController::class, 'store'])->name('store');
            Route::get('/{usuario}/editar', [UsuarioController::class, 'edit'])->name('edit');
            Route::put('/{usuario}', [UsuarioController::class, 'update'])->name('update');
            Route::post('/{usuario}/resetear-password', [UsuarioController::class, 'resetPassword'])->name('reset-password');
            Route::delete('/{usuario}', [UsuarioController::class, 'destroy'])->name('destroy');
        });

        Route::resource('colaboradores', ColaboradorController::class)->except('show')->parameters(['colaboradores' => 'colaborador']);
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
