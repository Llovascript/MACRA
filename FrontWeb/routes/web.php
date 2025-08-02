<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BeneficiarioPerfilController;
use App\Http\Controllers\PerfilDonanteController;
use App\Http\Controllers\UserDatabaseController;
use App\Http\Controllers\SolicitudesPerfilesController;

// Rutas principales
Route::get('/index', [UserController::class, 'showIndex'])->name('index');
Route::get('/', [UserController::class, 'showLoginRegister'])->name('home');
Route::get('/login', [UserController::class, 'showLoginRegister'])->name('login.register');

// Rutas API para autenticación
Route::post('/auth/login', [UserController::class, 'login'])->name('api.login');
Route::post('/auth/register', [UserController::class, 'register'])->name('api.register');

// Rutas protegidas
Route::middleware(['auth.check'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
});

// Ruta de logout
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// Rutas Administrador
Route::get('/menuAdmin', function () {
    return view('menuAdmin');
})->name('admin.menu');

Route::get('/adminSolicitudes', function () {
    return view('adminSolicitudes');
})->name('adminSolicitudes');

Route::get('/solicitudesPerfiles', [SolicitudesPerfilesController::class, 'index'])
    ->name('solicitudesPerfiles');

// Rutas Beneficiario
Route::get('/menuBeneficiario', function () {
    return view('menuBeneficiario');
})->name('beneficiario.menu');

Route::get('/perfilBeneficiario', [BeneficiarioPerfilController::class, 'index'])
    ->name('perfilBeneficiario');

// Rutas Donadores
Route::get('/menuDonantes', function () {
    return view('menuDonantes');
})->name('donante.menu');

Route::get('/perfilDonante', [PerfilDonanteController::class, 'index'])
    ->name('perfilDonante');

// Rutas de administración de usuarios (para desarrollo/testing)
Route::prefix('admin')->group(function () {
    Route::get('/check-database', [UserDatabaseController::class, 'checkDatabase'])->name('check.database');
    Route::get('/create-test-user', [UserDatabaseController::class, 'createTestUser'])->name('create.test.user');
    Route::get('/create-test-donante', [UserDatabaseController::class, 'createTestDonante'])->name('create.test.donante');
    Route::get('/create-pending-users', [UserDatabaseController::class, 'createTestPendingUsers'])->name('create.pending.users');
    Route::get('/check-reference-data', [UserDatabaseController::class, 'checkReferenceData'])->name('check.reference.data');
    
    // Rutas para gestionar solicitudes de perfiles
    Route::post('/solicitudes/{userId}/aprobar', [SolicitudesPerfilesController::class, 'aprobar'])->name('solicitudes.aprobar');
    Route::post('/solicitudes/{userId}/rechazar', [SolicitudesPerfilesController::class, 'rechazar'])->name('solicitudes.rechazar');
    Route::get('/solicitudes/estadisticas', [SolicitudesPerfilesController::class, 'estadisticas'])->name('solicitudes.estadisticas');
});