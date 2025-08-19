<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RainController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('formDashboard');
});

// Tambahkan ini
Route::resource('hujans', RainController::class);
Route::get('/rain', [RainController::class, 'index'])->name('rain.index'); // tampilkan data
Route::get('/rain/create', [RainController::class, 'create'])->name('rain.create'); // form tambah
Route::post('/rain', [RainController::class, 'store'])->name('rain.store'); // simpan data baru
Route::get('/rain/{id}/edit', [RainController::class, 'edit'])->name('rain.edit'); // form edit
Route::put('/rain/{id}', [RainController::class, 'update'])->name('rain.update'); // update data


