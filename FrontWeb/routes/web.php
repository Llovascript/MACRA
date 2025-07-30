<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventoController;

// Rutas públicas: login y registro
Route::get('/', [UserController::class, 'showLoginRegister'])->name('home');
Route::get('/login', [UserController::class, 'showLoginRegister'])->name('login'); // Esta es importante
Route::get('/login/register', [UserController::class, 'showLoginRegister'])->name('login.register');

// Rutas API para autenticación (JavaScript)
Route::post('/auth/login', [UserController::class, 'login'])->name('api.login');
Route::post('/auth/register', [UserController::class, 'register'])->name('api.register');

// Rutas protegidas con middleware auth.check
Route::middleware(['auth.check'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

    // Rutas para eventos
    Route::prefix('admin/eventos')->group(function () {
        Route::get('/', [EventoController::class, 'menu'])->name('admin.eventos.menu');
        Route::get('/crear', [EventoController::class, 'create'])->name('admin.eventos.create');
        Route::post('/crear', [EventoController::class, 'store'])->name('admin.eventos.store');

        Route::get('/actualizar', [EventoController::class, 'menuActualizar'])->name('admin.eventos.menu_actualizar');
        Route::get('/eliminar', [EventoController::class, 'menuEliminar'])->name('admin.eventos.menu_eliminar');
    });
});

// Ruta para logout
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
