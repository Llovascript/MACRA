<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserDatabaseController;
use App\Http\Controllers\PerfilController; 
use App\Http\Controllers\DonacionesController;

// 
// RUTAS PRINCIPALES
// 

Route::get('/index', [UserController::class, 'showIndex'])->name('index');
Route::get('/', [UserController::class, 'showLoginRegister'])->name('home');
Route::get('/login', [UserController::class, 'showLoginRegister'])->name('login.register');

// 
// RUTAS API PARA AUTENTICACIÓN
// 

Route::post('/auth/login', [UserController::class, 'login'])->name('api.login');
Route::post('/auth/register', [UserController::class, 'register'])->name('api.register');

// 
// RUTAS PROTEGIDAS
// 

Route::middleware(['auth.check'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
});

// 
// RUTA DE LOGOUT
// 

Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// 
// RUTAS ADMINISTRADOR
// 

// Menú principal del administrador
Route::get('/menuAdmin', [AdminController::class, 'index'])->name('admin.menu');

// Rutas de solicitudes
Route::get('/adminSolicitudes', function () {
    return view('adminSolicitudes');
})->name('adminSolicitudes');

// Rutas de estadísticas y API para admin /// no se usan pero tienen que estar para compatibilidad
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/estadisticas', [AdminController::class, 'getEstadisticas'])->name('estadisticas');
    Route::get('/usuarios-recientes', [AdminController::class, 'getUsuariosRecientes'])->name('usuarios.recientes');
    Route::get('/buscar-usuarios', [AdminController::class, 'buscarUsuarios'])->name('usuarios.buscar');
    Route::put('/usuarios/{id}/aprobacion', [AdminController::class, 'cambiarAprobacion'])->name('usuarios.aprobacion');
});

// 
// RUTAS BENEFICIARIO (USUARIOS FINALES)
// 

Route::get('/menuBeneficiario', function () {
    return view('menuBeneficiario');
})->name('beneficiario.menu');

Route::get('/perfilBeneficiario', function () {
    return view('perfilBeneficiario');
})->name('perfilBeneficiario');

// 
// RUTAS DONANTES (USUARIOS FINALES)
// 

Route::get('/menuDonantes', function () {
    return view('menuDonantes');
})->name('donante.menu');

Route::get('/perfilDonante', function () {
    return view('perfilDonante');
})->name('perfilDonante');

// 
// RUTAS DE ADMINISTRACIÓN DE USUARIOS (DESARROLLO/TESTING)
// 

Route::prefix('admin')->group(function () {
    Route::get('/check-database', [UserDatabaseController::class, 'checkDatabase'])->name('check.database');
    Route::get('/create-test-user', [UserDatabaseController::class, 'createTestUser'])->name('create.test.user');
    Route::get('/check-reference-data', [UserDatabaseController::class, 'checkReferenceData'])->name('check.reference.data');
});



// 
//  CRUD DE PERFILES 
// 

// Menú principal 
Route::get('/adminPerfiles', [PerfilController::class, 'index'])->name('adminPerfiles');

// Rutas principales 
Route::prefix('perfiles')->name('perfiles.')->group(function () {
    Route::get('/', [PerfilController::class, 'index'])->name('index');
    Route::get('/agregar', [PerfilController::class, 'create'])->name('create');
    Route::post('/agregar', [PerfilController::class, 'store'])->name('store');
    Route::get('/actualizar', [PerfilController::class, 'updateForm'])->name('updateForm');
    Route::put('/actualizar/{id}', [PerfilController::class, 'update'])->name('update');
    Route::get('/eliminar', [PerfilController::class, 'deleteForm'])->name('deleteForm');
    Route::delete('/eliminar/{id}', [PerfilController::class, 'destroy'])->name('destroy');
    Route::get('/buscar', [PerfilController::class, 'search'])->name('search');
    Route::get('/obtener/{id}', [PerfilController::class, 'show'])->name('show');
});

// Rutas de compatibilidad (para enlaces que apunten a las rutas antiguas)
Route::get('/adminBeneficiarios', function () {
    return redirect()->route('adminPerfiles');
})->name('adminBeneficiarios');

Route::get('/adminDonante', function () {
    return redirect()->route('adminPerfiles');
})->name('adminDonante');

// 
// RUTAS DE DONACIONES PARA DONANTES
// 

Route::middleware(['auth.check'])->group(function () {
    // Estatus de solicitudes (tabla principal)
    Route::get('/donaciones/estatus', [DonacionesController::class, 'estatusSolicitudes'])->name('donaciones.estatus');
    
    // Formulario para agregar donación
    Route::get('/donaciones/agregar', [DonacionesController::class, 'agregarDonacion'])->name('donaciones.agregar');
    
    // Procesar nueva donación
    Route::post('/donaciones/guardar', [DonacionesController::class, 'guardarDonacion'])->name('donaciones.guardar');
    
    // API para obtener detalles de donación (AJAX)
    Route::get('/donaciones/obtener/{id}', [DonacionesController::class, 'obtenerDonacion'])->name('donaciones.obtener');
});