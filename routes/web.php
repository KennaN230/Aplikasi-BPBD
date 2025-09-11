<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KejadianController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\RainController;

Route::view('/', 'welcome')->name('home');
Route::view('/admin', 'Admin')->name('admin.home');

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login')->middleware('guest');
    Route::post('/login','login')->name('login.process')->middleware('guest');

    Route::get('/register','showRegisterForm')->name('register')->middleware('guest');
    Route::post('/register','registerProcess')->name('register.process')->middleware('guest');

    Route::get('/lupa-password','showForgotPasswordForm')->name('password.request')->middleware('guest');
    Route::post('/lupa-password','sendResetLinkEmail')->name('password.email')->middleware('guest');

    Route::post('/logout','logout')->name('logout')->middleware('auth');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile',   [DashboardAdminController::class, 'editProfile'])->name('profile.edit');
    Route::patch('/profile', [DashboardAdminController::class, 'updateProfile'])->name('profile.update');

    Route::get('/kejadian', [KejadianController::class, 'index'])->name('kejadian');
    Route::get('/kejadian/{kejadian}', [KejadianController::class, 'show'])->name('kejadian.show');
});

Route::middleware(['auth','role:Admin'])->group(function () {
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');

    // CRUD users (pakai {id} agar tidak bergantung binding default "id")
    Route::resource('users', UserController::class)
        ->parameters(['users' => 'id'])
        ->except(['show']);

    // Approve / Reject
    Route::post('/users/{id}/approve', [UserController::class,'approve'])->name('users.approve');
    Route::post('/users/{id}/reject',  [UserController::class,'reject'])->name('users.reject');
});
    Route::get('/landing', [LandingController::class, 'index'])->name('landing');
    Route::get('/hujan', [RainController::class, 'index'])->name('Rain');
    Route::resource('rain', RainController::class);
    Route::get('/rain', [RainController::class, 'index'])->name('rain.index');
    Route::get('/tambahrain/create', [RainController::class, 'create'])->name('rain.create');
    Route::post('/rain', [RainController::class, 'store'])->name('rain.store');
    Route::get('/rain/{id}/edit', [RainController::class, 'edit'])->name('rain.edit');
    Route::get('/editrain', [RainController::class, 'update'])->name('rain.update');
    Route::post('/rain', [RainController::class, 'store'])->name('rain.store');
    Route::delete('/rain/{id}', [RainController::class, 'destroy'])->name('rain.destroy');
    Route::get('/rain/cetakpdf', [RainController::class, 'cetakpdf'])->name('rain.cetakpdf');
