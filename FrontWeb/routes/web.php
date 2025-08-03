<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
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

// Rutas de autenticación
Route::post('/auth/login', [UserController::class, 'login'])->name('api.login');
Route::post('/auth/register', [UserController::class, 'register'])->name('api.register');

// Rutas de logout
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
Route::get('/logout', [UserController::class, 'logoutGet'])->name('logout.get');

// Rutas protegidas - Dashboard genérico
Route::middleware(['auth.check'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
});

// Rutas de Administrador
Route::middleware(['auth.check:admin'])->group(function () {
    Route::get('/menuAdmin', [UserController::class, 'menuAdmin'])->name('admin.menu');
    Route::get('/adminSolicitudes', function () {
        return view('adminSolicitudes');
    })->name('adminSolicitudes');
    
    // Rutas de admin donantes
    Route::get('/adminDonantes', function () {
        return view('adminDonantes');
    })->name('adminDonantes');
    
    Route::get('/agregarDonante', function () {
        return view('agregarDonante');
    })->name('agregarDonante');
    
    Route::get('/actualizarDonante', function () {
        return view('actualizarDonante');
    })->name('actualizarDonante');
    
    Route::get('/eliminarDonante', function () {
        return view('eliminarDonante');
    })->name('eliminarDonante');
    
    // Rutas de admin beneficiarios
    Route::get('/adminBeneficiarios', function () {
        return view('adminBeneficiarios');
    })->name('adminBeneficiarios');
    
    Route::get('/agregarBeneficiario', function () {
        return view('agregarBeneficiario');
    })->name('agregarBeneficiario');
    
    Route::get('/actualizarBeneficiario', function () {
        return view('actualizarBeneficiario');
    })->name('actualizarBeneficiario');
    
    Route::get('/eliminarBeneficiario', function () {
        return view('eliminarBeneficiario');
    })->name('eliminarBeneficiario');
    
    Route::get('/solicitudesPerfiles', [SolicitudesPerfilesController::class, 'index'])
        ->name('solicitudesPerfiles');
    
    Route::get('/solicitudesDonaciones', [SolicitudesDonacionesController::class, 'index'])
        ->name('solicitudesDonaciones');
});

// Rutas de Beneficiario - SOLO BENEFICIARIOS (rol_id = 3)
Route::middleware(['auth.check:beneficiario'])->group(function () {
    Route::get('/menuBeneficiario', [UserController::class, 'menuBeneficiario'])->name('beneficiario.menu');
    Route::get('/perfilBeneficiario', [BeneficiarioPerfilController::class, 'index'])
        ->name('perfilBeneficiario');
});

// Rutas de Donantes - SOLO DONANTES (rol_id = 2)
Route::middleware(['auth.check:donante'])->group(function () {
    Route::get('/menuDonantes', [UserController::class, 'menuDonantes'])->name('donante.menu');
    Route::get('/perfilDonante', [PerfilDonanteController::class, 'index'])
        ->name('perfilDonante');
});

// Rutas de administración - SOLO ADMIN
Route::middleware(['auth.check:admin'])->prefix('admin')->group(function () {
    // Gestión de usuarios
    Route::get('/check-database', [UserDatabaseController::class, 'checkDatabase'])->name('check.database');
    Route::get('/create-test-user', [UserDatabaseController::class, 'createTestUser'])->name('create.test.user');
    Route::get('/create-test-donante', [UserDatabaseController::class, 'createTestDonante'])->name('create.test.donante');
    Route::get('/create-pending-users', [UserDatabaseController::class, 'createTestPendingUsers'])->name('create.pending.users');
    Route::get('/check-reference-data', [UserDatabaseController::class, 'checkReferenceData'])->name('check.reference.data');
    
    // Gestión de solicitudes de perfiles
    Route::post('/solicitudes/{userId}/aprobar', [SolicitudesPerfilesController::class, 'aprobar'])->name('solicitudes.aprobar');
    Route::post('/solicitudes/{userId}/rechazar', [SolicitudesPerfilesController::class, 'rechazar'])->name('solicitudes.rechazar');
    Route::get('/solicitudes/estadisticas', [SolicitudesPerfilesController::class, 'estadisticas'])->name('solicitudes.estadisticas');
    
    // Gestión de donaciones
    Route::get('/donaciones/check-structure', [DonacionesDatabaseController::class, 'checkDonacionesStructure'])->name('donaciones.check.structure');
    Route::get('/donaciones/check-data', [DonacionesDatabaseController::class, 'checkAvailableData'])->name('donaciones.check.data');
    Route::get('/donaciones/create-categories', [DonacionesDatabaseController::class, 'createBasicCategories'])->name('donaciones.create.categories');
    Route::get('/donaciones/create-unidades', [DonacionesDatabaseController::class, 'createBasicUnidades'])->name('donaciones.create.unidades');
    Route::get('/donaciones/create-test', [DonacionesDatabaseController::class, 'createTestDonaciones'])->name('donaciones.create.test');
    Route::get('/donaciones/get-completas', [DonacionesDatabaseController::class, 'getDonacionesCompletas'])->name('donaciones.get.completas');
    Route::get('/donaciones/clear-test', [DonacionesDatabaseController::class, 'clearTestDonaciones'])->name('donaciones.clear.test');
    Route::get('/donaciones/create-articulos', [DonacionesDatabaseController::class, 'createTestArticulos'])->name('donaciones.create.articulos');
    
    // Gestión de solicitudes de donaciones
    Route::post('/donaciones/{donacionId}/aprobar', [SolicitudesDonacionesController::class, 'aprobar'])->name('donaciones.aprobar');
    Route::post('/donaciones/{donacionId}/rechazar', [SolicitudesDonacionesController::class, 'rechazar'])->name('donaciones.rechazar');
    Route::get('/donaciones/estadisticas', [SolicitudesDonacionesController::class, 'estadisticas'])->name('donaciones.estadisticas');
});

// Ruta de redirección automática por rol
Route::middleware(['auth.check'])->group(function () {
    Route::get('/redirect-by-role', function () {
        $user = Session::get('user');
        $roleId = $user['rol_id'] ?? null;
        
        switch ($roleId) {
            case 1:
                return redirect()->route('admin.menu');
            case 2:
                return redirect()->route('donante.menu');
            case 3:
                return redirect()->route('beneficiario.menu');
            default:
                return redirect()->route('dashboard');
        }
    })->name('redirect.by.role');
});