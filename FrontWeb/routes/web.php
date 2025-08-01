<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventoController;

// Rutas públicas: login y registro
Route::get('/index', [UserController::class, 'showIndex'])->name('index');
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
    });

    // ✅ Rutas para beneficiarios (fuera del prefijo de admin)
    Route::get('/eventos/usuario', [EventoController::class, 'verEventosDisponibles'])->name('eventos.usuario');
    Route::post('/eventos/unirse/{id}', [EventoController::class, 'unirseEvento'])->name('eventos.unirse');
});

// Ruta para logout
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// Rutas Admministrador
Route::get('/menuAdmin', function () {
    return view('menuAdmin');
})->name('admin.menu');

Route::get('/adminSolicitudes', function () {
    return view('adminSolicitudes');
})->name('adminSolicitudes');


// Rutas Beneficiario
Route::get('/menuBeneficiario', function () {
    return view('menuBeneficiario');
})->name('beneficiario.menu');

Route::get('/perfilBeneficiario', function () {
    return view('perfilBeneficiario');
})->name('perfilBeneficiario');


// Rutas Donadores
Route::get('/menuDonantes', function () {
    return view('menuDonantes');
})->name('donante.menu');

Route::get('/perfilDonante', function () {
    return view('perfilDonante');
})->name('perfilDonante');
