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
