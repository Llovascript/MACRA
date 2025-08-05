<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\DonacionController;

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

    // Rutas para administración de eventos (admin)
    Route::prefix('admin/eventos')->group(function () {
        Route::get('/', [EventoController::class, 'menu'])->name('admin.eventos.menu');
        Route::get('/crear', [EventoController::class, 'create'])->name('admin.eventos.create');
        Route::post('/crear', [EventoController::class, 'store'])->name('admin.eventos.store');
        Route::get('/gestionar', [EventoController::class, 'manage'])->name('admin.eventos.manage');
        Route::delete('/{id}', [EventoController::class, 'destroy'])->name('admin.eventos.destroy');
        Route::get('/{id}/editar', [EventoController::class, 'edit'])->name('admin.eventos.edit');
        Route::post('/{id}/actualizar', [EventoController::class, 'update'])->name('admin.eventos.update');
        Route::get('/capacidad', [EventoController::class, 'capacidad'])->name('admin.eventos.capacidad');
    });

    // Rutas para beneficiarios
    Route::get('/eventos/usuario', [EventoController::class, 'verEventosDisponibles'])->name('eventos.usuario');
    Route::post('/eventos/unirse/{id}', [EventoController::class, 'unirseEvento'])->name('eventos.unirse');
    Route::delete('/eventos/salir/{id}', [EventoController::class, 'salirEventoBeneficiario'])->name('eventos.salir.beneficiario');

    // Rutas para donantes
    Route::get('/eventos/donante', [EventoController::class, 'verEventosDisponiblesDonante'])->name('eventos.donante');
    Route::post('/eventos/donante/unirse/{id}', [EventoController::class, 'unirseEventoDonante'])->name('eventos.unirse.donante');
    Route::delete('/eventos/donante/salir/{id}', [EventoController::class, 'salirEventoDonante'])->name('eventos.salir.donante');

    // Ruta adicional para verificar participación (opcional)
    Route::get('/eventos/{id}/participacion', [EventoController::class, 'verificarParticipacion'])->name('eventos.participacion');

    // ✅ Sistema completo de donaciones
    Route::prefix('donaciones')->group(function () {
        Route::get('/crear', [DonacionController::class, 'crear'])->name('donaciones.crear');
        Route::post('/guardar', [DonacionController::class, 'guardar'])->name('donaciones.guardar');
        Route::get('/historial', [DonacionController::class, 'historial'])->name('donaciones.historial');
    });
});

// Ruta para logout
Route::post('/logout', [UserController::class, 'logout'])->name('logout');