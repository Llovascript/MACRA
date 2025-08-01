<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

// Rutas Admministrador
Route::get('/menuAdmin', function () {
    return view('menuAdmin');
})->name('admin.menu');

Route::get('/adminSolicitudes', function () {
    return view('adminSolicitudes');
})->name('adminSolicitudes');

//ruta para agregar beneficiario
Route::get('/agregarBeneficiario', function () {
    return view('agregarBeneficiario');
})->name('agregarBeneficiario');

//ruta para actualizar beneficiario
Route::get('/actualizarBeneficiario', function () {
    return view('actualizarBeneficiario');
})->name('actualizarBeneficiario');

//Ruta para eliminar beneficiario
Route::get('/eliminarBeneficiario', function () {
    return view('eliminarBeneficiario');
})->name('eliminarBeneficiario');


// Rutas Beneficiario
Route::get('/menuBeneficiario', function () {
    return view('menuBeneficiario');
})->name('beneficiario.menu');

Route::get('/perfilBeneficiario', function () {
    return view('perfilBeneficiario');
})->name('perfilBeneficiario');

//ruta adminBeneficiario
Route::get('/adminBeneficiario', function () {
    return view('adminBeneficiarios');
})->name('adminBeneficiarios');

//ruta para agregar donante
Route::get('/agregarDonante', function () {
    return view('agregarDonante');
})->name('agregarDonante');

//ruta para actualizar donante
Route::get('/actualizarDonante', function () {
    return view('actualizarDonante');
})->name('actualizarDonante');

//ruta para eliminar donante
Route::get('/eliminarDonante', function () {
    return view('eliminarDonante');
})->name('eliminarDonante');






// Rutas Donadores
Route::get('/menuDonantes', function () {
    return view('menuDonantes');
})->name('donante.menu');

Route::get('/perfilDonante', function () {
    return view('perfilDonante');
})->name('perfilDonante');

//ruta adminDonante
Route::get('/adminDonante', function () {
    return view('adminDonantes');
})->name('adminDonante');

