<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KejadianController;
use App\Http\Controllers\RainController;

/*
|--------------------------------------------------------------------------
| Halaman Utama
|--------------------------------------------------------------------------
*/
Route::view('/', 'welcome')->name('home');
Route::view('/dashboard', 'formDashboard')->name('dashboard');
Route::view('/admin', 'Admin')->name('admin.home');

/*
|--------------------------------------------------------------------------
| Modul Kejadian
|--------------------------------------------------------------------------
*/
Route::get('/kejadian', [KejadianController::class, 'index'])->name('kejadian');

/*
|--------------------------------------------------------------------------
| Modul Curah Hujan (Rain)
|--------------------------------------------------------------------------
*/
Route::get('/hujan', [RainController::class, 'index'])->name('rain.index');
Route::get('/hujan/tambah', [RainController::class, 'create'])->name('rain.create');
Route::post('/hujan/store', [RainController::class, 'store'])->name('rain.store');
Route::get('/rain/{id}/edit', [RainController::class, 'edit'])->name('rain.edit');
Route::put('/rain/{id}', [RainController::class, 'update'])->name('rain.update');
Route::put('/hujan/update/{rain}', [RainController::class, 'update'])->name('rain.update');
Route::delete('/hujan/delete/{rain}', [RainController::class, 'destroy'])->name('rain.destroy');

// ✅ Cetak PDF tabel dan grafik
Route::get('/rain/cetakpdf', [RainController::class, 'cetakpdf'])->name('rain.cetakpdf');
Route::get('/rainpdfgrafik', [RainController::class, 'cetakPdfGrafik'])->name('rainpdfgrafik');

/*
|--------------------------------------------------------------------------
| Autentikasi (Login, Register, Lupa Password)
|--------------------------------------------------------------------------
*/
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login')->name('login.process');

    Route::get('/register', 'showRegisterForm')->name('register');
    Route::post('/register', 'registerProcess')->name('register.process');

    Route::get('/lupa-password', 'showForgotPasswordForm')->name('password.request');
    Route::post('/lupa-password', 'sendResetLinkEmail')->name('password.email');
});

use App\Http\Controllers\GelombangController;



// 💡 TARUH DUA ROUTE INI SEBELUM resource!
Route::get('/gelombang/cetakpdf', [GelombangController::class, 'gelombangpdf'])->name('gelombang.cetakpdf');
Route::get('/gelombang/pdfgrafik', [GelombangController::class, 'pdfgrafik'])->name('gelombang.pdfgrafik');

// Baru ini di bawah
Route::resource('gelombang', GelombangController::class);
