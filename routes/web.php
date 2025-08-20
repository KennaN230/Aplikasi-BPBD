<?php

use App\Http\Controllers\GempaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KejadianController;

// Halaman Welcome
Route::view('/', 'welcome')->name('home');

// Halaman Utama (Dashboard, Kejadian, Admin)
Route::view('/dashboard', 'formDashboard')->name('dashboard');
Route::get('/kejadian', [KejadianController::class, 'index'])->name('kejadian');

Route::get('/gempa', [GempaController::class, 'index'])->name('gempa');
 
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

    // Gempa Bumi
    
});
