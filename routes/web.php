<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\{
    AuthController,
    LupaPasswordController,
    KejadianController,
    DashboardAdminController,
    UserController,
    LandingController,
    RainController,
    AktivitasGunungController,
    KaryawanController,
    DesaController
};

/*
|--------------------------------------------------------------------------
| Halaman Utama
|--------------------------------------------------------------------------
*/
Route::view('/', 'welcome')->name('home');
Route::view('/admin', 'Admin')->name('admin.home');

/*
|--------------------------------------------------------------------------
| AUTH (Login, Register, Logout)
|--------------------------------------------------------------------------
*/
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login')->middleware('guest');
    Route::post('/login', 'login')->name('login.process')->middleware('guest');

    Route::get('/register', 'showRegisterForm')->name('register')->middleware('guest');
    Route::post('/register', 'registerProcess')->name('register.process')->middleware('guest');

    Route::post('/logout', 'logout')->name('logout')->middleware('auth');
});

/*
|--------------------------------------------------------------------------
| LUPA / RESET PASSWORD (LupaPasswordController)
|--------------------------------------------------------------------------
*/
Route::controller(LupaPasswordController::class)
    ->middleware('guest')
    ->group(function () {
        Route::get('/lupa-password', 'create')->name('password.request');
        Route::post('/lupa-password', 'store')->name('password.email');
        Route::get('/reset-password/{token}', 'edit')->name('password.reset');
        Route::post('/reset-password', 'update')->name('password.update');
    });

/*
|--------------------------------------------------------------------------
| AREA AUTHENTICATED (User login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile',   [DashboardAdminController::class, 'editProfile'])->name('profile.edit');
    Route::patch('/profile', [DashboardAdminController::class, 'updateProfile'])->name('profile.update');

    // Laporan harian dan cetak
    Route::get('/beranda/cetak-laporan-eoc', [AuthController::class, 'printLaphar'])->name('beranda.cetak-laphar');
    Route::get('/laporan-harian', [AuthController::class, 'laporanHarian'])->name('laporan.harian');
});

/*
|--------------------------------------------------------------------------
| ADMIN ONLY
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)
        ->parameters(['users' => 'id'])
        ->except(['show']);

    Route::post('/users/{id}/approve', [UserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{id}/reject',  [UserController::class, 'reject'])->name('users.reject');
});

/*
|--------------------------------------------------------------------------
| MODUL KEJADIAN
|--------------------------------------------------------------------------
*/
Route::controller(KejadianController::class)->group(function () {
    Route::get('/kejadian', 'index')->name('kejadian');
    Route::get('/kejadian/create', 'create')->name('kejadian.create');
    Route::post('/kejadian', 'store')->name('kejadian.store');
    Route::get('/kejadian/{id_kejadian}/edit', 'edit')->name('kejadian.edit');
    Route::put('/kejadian/{id_kejadian}', 'update')->name('kejadian.update');
    Route::delete('/kejadian/{id_kejadian}', 'destroy')->name('kejadian.destroy');
    Route::get('/kejadian/{id_kejadian}/print', 'print')->name('kejadian.print');
    Route::get('/kejadian/filter', 'filter')->name('kejadian.filter');
    Route::get('/kejadian/{id_kejadian}', 'show')->name('kejadian.show');
    Route::get('/kejadian/print', 'printByTanggal')->name('kejadian.printByTanggal');
});
Route::get('/get-tb_desa/{id_kecamatan}', [DesaController::class, 'getDesa'])->name('desa.get');

/*
|--------------------------------------------------------------------------
| MODUL PETUGAS / KARYAWAN
|--------------------------------------------------------------------------
*/
Route::controller(KaryawanController::class)->group(function () {
    Route::get('/petugas', 'index')->name('karyawan.index');
    Route::post('/petugas', 'store')->name('karyawan.store');
    Route::delete('/petugas/{nip_pengawas}', 'destroy')->name('karyawan.destroy');
});

/*
|--------------------------------------------------------------------------
| MODUL CURAH HUJAN (RAIN)
|--------------------------------------------------------------------------
*/
Route::controller(RainController::class)->group(function () {
    Route::get('/hujan', 'index')->name('rain.index');
    Route::get('/hujan/tambah', 'create')->name('rain.create');
    Route::post('/hujan/store', 'store')->name('rain.store');
    Route::get('/rain/{id}/edit', 'edit')->name('rain.edit');
    Route::put('/rain/{id}', 'update')->name('rain.update');
    Route::put('/hujan/update/{rain}', 'update')->name('rain.update');
    Route::delete('/hujan/delete/{rain}', 'destroy')->name('rain.destroy');

    // Cetak PDF
    Route::get('/rain/cetakpdf', 'cetakpdf')->name('rain.cetakpdf');
    Route::get('/rainpdfgrafik', 'cetakPdfGrafik')->name('rainpdfgrafik');
});

/*
|--------------------------------------------------------------------------
| MODUL LANDING PAGE + API PETA
|--------------------------------------------------------------------------
*/
Route::get('/landing', [LandingController::class, 'index'])->name('landing');

// API kejadian di peta
Route::get('/kejadian/daerah/{kecamatan}', [LandingController::class, 'listKejadianDaerah']);
Route::get('/kejadian/daerah/{kecamatan}/{jenis}', [LandingController::class, 'detailKejadianDaerah']);
Route::get('/kejadian/daerah/{kecamatan}/desa/{desa}', [LandingController::class, 'listKejadianDesa']);
Route::get('/kejadian/daerah/{kecamatan}/desa/{desa}/{jenis}', [LandingController::class, 'detailKejadianDesa']);

/*
|--------------------------------------------------------------------------
| MODUL AKTIVITAS GUNUNG
|--------------------------------------------------------------------------
*/
Route::get('/aktivitas-gunung/export', [AktivitasGunungController::class, 'exportXlsx'])->name('aktivitas-gunung.export');

// Hindari bentrok antara "export" dan "show"
Route::resource('aktivitas-gunung', AktivitasGunungController::class)->except(['show']);


Route::get('/kejadian/get-index', [KejadianController::class, 'getIndex'])->name('kejadian.getIndex');