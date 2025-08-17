<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// Halaman awal admin (biru-oranye)
Route::get('/Admin', function () {
    return view('Admin'); // resources/views/Admin.blade.php
})->name('admin.home');

// Dashboard setelah login
Route::get('/dashboard', function () {
    return view('formDashboard');
})->name('dashboard');

// Login routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');

// Register routes
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register'); // menampilkan form
Route::post('/register', [AuthController::class, 'registerProcess'])->name('register.process'); // memproses form


// route untuk forgot password
Route::get('/LupaPassword', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/LupaPassword', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');