<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BeneficiarioPerfilController;
use App\Http\Controllers\PerfilDonanteController;
use App\Http\Controllers\UserDatabaseController;
use App\Http\Controllers\SolicitudesPerfilesController;
use App\Http\Controllers\SolicitudesDonacionesController;
use App\Http\Controllers\DonacionesDatabaseController;

// Rutas principales
Route::get('/index', [UserController::class, 'showIndex'])->name('index');
Route::get('/', [UserController::class, 'showLoginRegister'])->name('home');
Route::get('/login', [UserController::class, 'showLoginRegister'])->name('login.register');

// Rutas API para autenticación
Route::post('/auth/login', [UserController::class, 'login'])->name('api.login');
Route::post('/auth/register', [UserController::class, 'register'])->name('api.register');

// Ruta de logout
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// RUTAS TEMPORALES PARA CREAR ROLES
Route::get('/create-roles', [UserController::class, 'createRoles'])->name('create.roles');
Route::get('/check-roles', [UserController::class, 'checkRoles'])->name('check.roles');

// Rutas protegidas - Dashboard genérico
Route::middleware(['auth.check'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
});

// Rutas de Administrador - PROTEGIDAS
Route::middleware(['auth.check'])->group(function () {
    Route::get('/menuAdmin', [UserController::class, 'menuAdmin'])->name('admin.menu');
    Route::get('/adminSolicitudes', function () {
        return view('adminSolicitudes');
    })->name('adminSolicitudes');
    
    Route::get('/solicitudesPerfiles', [SolicitudesPerfilesController::class, 'index'])
        ->name('solicitudesPerfiles');
    
    // Ruta para solicitudes de donaciones
    Route::get('/solicitudesDonaciones', [SolicitudesDonacionesController::class, 'index'])
        ->name('solicitudesDonaciones');
});

// Rutas de Beneficiario - PROTEGIDAS
Route::middleware(['auth.check'])->group(function () {
    Route::get('/menuBeneficiario', [UserController::class, 'menuBeneficiario'])->name('beneficiario.menu');
    Route::get('/perfilBeneficiario', [BeneficiarioPerfilController::class, 'index'])
        ->name('perfilBeneficiario');
});

// Rutas de Donantes - PROTEGIDAS
Route::middleware(['auth.check'])->group(function () {
    Route::get('/menuDonantes', [UserController::class, 'menuDonantes'])->name('donante.menu');
    Route::get('/perfilDonante', [PerfilDonanteController::class, 'index'])
        ->name('perfilDonante');
});

// Rutas de administración (para desarrollo/testing y gestión) - SOLO ADMIN
Route::middleware(['auth.check'])->prefix('admin')->group(function () {
    // Rutas para gestión de usuarios
    Route::get('/check-database', [UserDatabaseController::class, 'checkDatabase'])->name('check.database');
    Route::get('/create-test-user', [UserDatabaseController::class, 'createTestUser'])->name('create.test.user');
    Route::get('/create-test-donante', [UserDatabaseController::class, 'createTestDonante'])->name('create.test.donante');
    Route::get('/create-pending-users', [UserDatabaseController::class, 'createTestPendingUsers'])->name('create.pending.users');
    Route::get('/check-reference-data', [UserDatabaseController::class, 'checkReferenceData'])->name('check.reference.data');
    
    // Rutas para gestionar solicitudes de perfiles
    Route::post('/solicitudes/{userId}/aprobar', [SolicitudesPerfilesController::class, 'aprobar'])->name('solicitudes.aprobar');
    Route::post('/solicitudes/{userId}/rechazar', [SolicitudesPerfilesController::class, 'rechazar'])->name('solicitudes.rechazar');
    Route::get('/solicitudes/estadisticas', [SolicitudesPerfilesController::class, 'estadisticas'])->name('solicitudes.estadisticas');
    
    // Rutas para testing y desarrollo de donaciones
    Route::get('/donaciones/check-structure', [DonacionesDatabaseController::class, 'checkDonacionesStructure'])->name('donaciones.check.structure');
    Route::get('/donaciones/check-data', [DonacionesDatabaseController::class, 'checkAvailableData'])->name('donaciones.check.data');
    Route::get('/donaciones/create-categories', [DonacionesDatabaseController::class, 'createBasicCategories'])->name('donaciones.create.categories');
    Route::get('/donaciones/create-unidades', [DonacionesDatabaseController::class, 'createBasicUnidades'])->name('donaciones.create.unidades');
    Route::get('/donaciones/create-test', [DonacionesDatabaseController::class, 'createTestDonaciones'])->name('donaciones.create.test');
    Route::get('/donaciones/get-completas', [DonacionesDatabaseController::class, 'getDonacionesCompletas'])->name('donaciones.get.completas');
    Route::get('/donaciones/clear-test', [DonacionesDatabaseController::class, 'clearTestDonaciones'])->name('donaciones.clear.test');
    Route::get('/donaciones/create-articulos', [DonacionesDatabaseController::class, 'createTestArticulos'])->name('donaciones.create.articulos');
    
    // Rutas para aprobar/rechazar donaciones (AJAX)
    Route::post('/donaciones/{donacionId}/aprobar', [SolicitudesDonacionesController::class, 'aprobar'])->name('donaciones.aprobar');
    Route::post('/donaciones/{donacionId}/rechazar', [SolicitudesDonacionesController::class, 'rechazar'])->name('donaciones.rechazar');
    Route::get('/donaciones/estadisticas', [SolicitudesDonacionesController::class, 'estadisticas'])->name('donaciones.estadisticas');
});