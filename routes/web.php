<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KejadianController;
use App\Http\Controllers\RainController;

// Halaman Welcome
Route::view('/', 'welcome')->name('home');

// Halaman Utama (Dashboard, Kejadian, Admin)
Route::view('/dashboard', 'formDashboard')->name('dashboard');
Route::get('/kejadian', [KejadianController::class, 'index'])->name('kejadian');
Route::get('/hujan', [RainController::class, 'index'])->name('Rain');
Route::view('/admin', 'Admin')->name('admin.home'); // resources/views/Admin.blade.php

// Group Authentication Routes
Route::controller(AuthController::class)->group(function () {
    // Login
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login')->name('login.process');

    // Register
    Route::get('/register', 'showRegisterForm')->name('register');
    Route::post('/register', 'registerProcess')->name('register.process');

    // Forgot Password
    Route::get('/lupa-password', 'showForgotPasswordForm')->name('password.request');
    Route::post('/lupa-password', 'sendResetLinkEmail')->name('password.email');
});

// CRUD Rain

Route::resource('rain', RainController::class);
Route::get('/rain', [RainController::class, 'index'])->name('rain.index');
Route::get('/tambahrain/create', [RainController::class, 'create'])->name('rain.create');
Route::post('/rain', [RainController::class, 'store'])->name('rain.store');
Route::get('/rain/{id}/edit', [RainController::class, 'edit'])->name('rain.edit');
Route::get('/editrain', [RainController::class, 'update'])->name('rain.update');
Route::post('/rain', [RainController::class, 'store'])->name('rain.store');
Route::delete('/rain/{id}', [RainController::class, 'destroy'])->name('rain.destroy');
Route::get('/rain/cetakpdf', [RainController::class, 'cetakpdf'])->name('rain.cetakpdf');


