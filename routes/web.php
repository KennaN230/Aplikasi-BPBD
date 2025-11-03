<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LupaPasswordController;
use App\Http\Controllers\KejadianController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\RainController;
use App\Http\Controllers\AktivitasGunungController;

Route::view('/', 'welcome')->name('home');
Route::view('/admin', 'Admin')->name('admin.home');

/*
|--------------------------------------------------------------------------
| Auth (Login, Register, Logout)
|--------------------------------------------------------------------------
*/
Route::controller(AuthController::class)->group(function () {
    // Login & Register
    Route::get('/login', 'showLoginForm')->name('login')->middleware('guest');
    Route::post('/login', 'login')->name('login.process')->middleware('guest');

    Route::get('/register', 'showRegisterForm')->name('register')->middleware('guest');
    Route::post('/register', 'registerProcess')->name('register.process')->middleware('guest');

    // Logout
    Route::post('/logout', 'logout')->name('logout')->middleware('auth');
});

/*
|--------------------------------------------------------------------------
| Lupa / Reset Password  (pakai LupaPasswordController)
|--------------------------------------------------------------------------
*/
Route::controller(LupaPasswordController::class)
    ->middleware('guest')
    ->group(function () {
        // Form minta link reset
        Route::get('/lupa-password', 'create')->name('password.request');
        // Kirim email berisi link reset
        Route::post('/lupa-password', 'store')->name('password.email');

        // Form setel password baru (dibuka dari email)
        Route::get('/reset-password/{token}', 'edit')->name('password.reset');
        // Simpan password baru
        Route::post('/reset-password', 'update')->name('password.update');
    });

/*
|--------------------------------------------------------------------------
| Area Authenticated
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile',   [DashboardAdminController::class, 'editProfile'])->name('profile.edit');
    Route::patch('/profile', [DashboardAdminController::class, 'updateProfile'])->name('profile.update');

    Route::get('/kejadian', [KejadianController::class, 'index'])->name('kejadian');
    Route::get('/kejadian/{kejadian}', [KejadianController::class, 'show'])->name('kejadian.show');
});

/*
|--------------------------------------------------------------------------
| Admin Only
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','role:Admin'])->group(function () {
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)
        ->parameters(['users' => 'id'])
        ->except(['show']);

    Route::post('/users/{id}/approve', [UserController::class,'approve'])->name('users.approve');
    Route::post('/users/{id}/reject',  [UserController::class,'reject'])->name('users.reject');
});

/*
|--------------------------------------------------------------------------
| Landing + Hujan
|--------------------------------------------------------------------------
*/
Route::get('/landing', [LandingController::class, 'index'])->name('landing');

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
| API kecil untuk modal “Next” di peta
|--------------------------------------------------------------------------
*/
Route::get('/kejadian/daerah/{kecamatan}', [LandingController::class, 'listKejadianDaerah']);
Route::get('/kejadian/daerah/{kecamatan}/{jenis}', [LandingController::class, 'detailKejadianDaerah']);

// API desa
Route::get('/kejadian/daerah/{kecamatan}/desa/{desa}', [LandingController::class, 'listKejadianDesa']);
Route::get('/kejadian/daerah/{kecamatan}/desa/{desa}/{jenis}', [LandingController::class, 'detailKejadianDesa']);

Route::get('/aktivitas-gunung/export', [AktivitasGunungController::class, 'exportXlsx'])
    ->name('aktivitas-gunung.export');

// Resource tanpa show supaya path /aktivitas-gunung/export tidak disangka show('export')
Route::resource('aktivitas-gunung', AktivitasGunungController::class)->except(['show']);

Route::get('/beranda/cetak-laporan-eoc', [AuthController::class, 'printLaphar'])
    ->name('beranda.cetak-laphar')   // opsional, karena di Blade kita pakai url()
    ->middleware(['auth']);   
    

Route::get('/laporan-harian', [AuthController::class, 'laporanHarian'])
     ->name('laporan.harian'); // HTML preview + tombol Cetak (window.print)
