<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BeneficiarioController;
use App\Http\Controllers\DonanteController;
use App\Http\Controllers\AdminController;

// Rutas para mostrar vistas
Route::get('/index', [UserController::class, 'showIndex'])->name('index');
Route::get('/', [UserController::class, 'showLoginRegister'])->name('home');
Route::get('/login', [UserController::class, 'showLoginRegister'])->name('login.register');

// Rutas API para autenticación (estas son las que usará JavaScript)
Route::post('/auth/login', [UserController::class, 'login'])->name('api.login');
Route::post('/auth/register', [UserController::class, 'register'])->name('api.register');

// Rutas protegidas
Route::middleware(['auth.check'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
});

// Ruta de logout (sin middleware porque necesita limpiar la sesión)
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// ==========================================
// RUTAS ADMINISTRADOR
// ==========================================

// Menú principal del administrador
Route::get('/menuAdmin', [AdminController::class, 'index'])->name('admin.menu');

// Rutas de estadísticas y API para admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/estadisticas', [AdminController::class, 'getEstadisticas'])->name('estadisticas');
    Route::get('/usuarios-recientes', [AdminController::class, 'getUsuariosRecientes'])->name('usuarios.recientes');
    Route::get('/buscar-usuarios', [AdminController::class, 'buscarUsuarios'])->name('usuarios.buscar');
    Route::put('/usuarios/{id}/aprobacion', [AdminController::class, 'cambiarAprobacion'])->name('usuarios.aprobacion');
});

Route::get('/adminSolicitudes', function () {
    return view('adminSolicitudes');
})->name('adminSolicitudes');

// ==========================================
// RUTAS PARA BENEFICIARIOS
// ==========================================

// Menú de administración de beneficiarios
Route::get('/adminBeneficiarios', [AdminController::class, 'beneficiariosMenu'])->name('adminBeneficiarios');

// CRUD Beneficiarios
Route::prefix('beneficiarios')->name('beneficiarios.')->group(function () {
    // Crear beneficiario
    Route::get('/agregar', [BeneficiarioController::class, 'create'])->name('agregar');
    Route::post('/agregar', [BeneficiarioController::class, 'store'])->name('store');
    
    // Actualizar beneficiario
    Route::get('/actualizar', [BeneficiarioController::class, 'updateForm'])->name('actualizar');
    Route::put('/actualizar/{id}', [BeneficiarioController::class, 'update'])->name('update');
    
    // Eliminar beneficiario
    Route::get('/eliminar', [BeneficiarioController::class, 'deleteForm'])->name('eliminar');
    Route::delete('/eliminar/{id}', [BeneficiarioController::class, 'destroy'])->name('destroy');
    
    // API endpoints para AJAX
    Route::get('/buscar', [BeneficiarioController::class, 'search'])->name('search');
    Route::get('/obtener/{id}', [BeneficiarioController::class, 'show'])->name('show');
});

// Rutas adicionales para compatibilidad con nombres existentes
Route::get('/agregarBeneficiario', [BeneficiarioController::class, 'create'])->name('agregarBeneficiario');
Route::post('/agregarBeneficiario', [BeneficiarioController::class, 'store']);
Route::get('/actualizarBeneficiario', [BeneficiarioController::class, 'updateForm'])->name('actualizarBeneficiario');
Route::put('/actualizarBeneficiario/{id}', [BeneficiarioController::class, 'update']);
Route::get('/eliminarBeneficiario', [BeneficiarioController::class, 'deleteForm'])->name('eliminarBeneficiario');
Route::delete('/eliminarBeneficiario/{id}', [BeneficiarioController::class, 'destroy']);

// ==========================================
// RUTAS PARA DONANTES
// ==========================================

// Menú de administración de donantes
Route::get('/adminDonante', [AdminController::class, 'donantesMenu'])->name('adminDonante');

// CRUD Donantes
Route::prefix('donantes')->name('donantes.')->group(function () {
    // Crear donante
    Route::get('/agregar', [DonanteController::class, 'create'])->name('agregar');
    Route::post('/agregar', [DonanteController::class, 'store'])->name('store');
    
    // Actualizar donante
    Route::get('/actualizar', [DonanteController::class, 'updateForm'])->name('actualizar');
    Route::put('/actualizar/{id}', [DonanteController::class, 'update'])->name('update');
    
    // Eliminar donante
    Route::get('/eliminar', [DonanteController::class, 'deleteForm'])->name('eliminar');
    Route::delete('/eliminar/{id}', [DonanteController::class, 'destroy'])->name('destroy');
    
    // API endpoints para AJAX
    Route::get('/buscar', [DonanteController::class, 'search'])->name('search');
    Route::get('/obtener/{id}', [DonanteController::class, 'show'])->name('show');
});

// Rutas adicionales para compatibilidad con nombres existentes
Route::get('/agregarDonante', [DonanteController::class, 'create'])->name('agregarDonante');
Route::post('/agregarDonante', [DonanteController::class, 'store']);
Route::get('/actualizarDonante', [DonanteController::class, 'updateForm'])->name('actualizarDonante');
Route::put('/actualizarDonante/{id}', [DonanteController::class, 'update']);
Route::get('/eliminarDonante', [DonanteController::class, 'deleteForm'])->name('eliminarDonante');
Route::delete('/eliminarDonante/{id}', [DonanteController::class, 'destroy']);

// ==========================================
// RUTAS ADICIONALES EXISTENTES
// ==========================================

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

