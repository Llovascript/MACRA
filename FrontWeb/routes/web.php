<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventoController;

// Rutas públicas: login y registro
Route::get('/', [UserController::class, 'showLoginRegister'])->name('home');
Route::get('/login', [UserController::class, 'showLoginRegister'])->name('login');
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

        Route::get('/gestionar', [EventoController::class, 'manage'])->name('admin.eventos.manage');
        Route::delete('/{id}', [EventoController::class, 'destroy'])->name('admin.eventos.destroy');
        Route::get('/{id}/editar', [EventoController::class, 'edit'])->name('admin.eventos.edit');
        Route::post('/{id}/actualizar', [EventoController::class, 'update'])->name('admin.eventos.update');
    });
});

// Ruta para logout
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
