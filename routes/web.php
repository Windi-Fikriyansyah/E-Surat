<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Manajemen Pengguna (Admin)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/data', [UserController::class, 'data'])->name('data');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
    });

    // Manajemen Role & Hak Akses (Admin)
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::post('/data', [RoleController::class, 'data'])->name('data');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{id}', [RoleController::class, 'show'])->name('show');
        Route::put('/{id}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{id}', [RoleController::class, 'destroy'])->name('destroy');
    });

    // Menu Surat (Surat Masuk & Surat Keluar)
    Route::prefix('surat')->name('surat.')->group(function () {
        Route::get('/', [SuratController::class, 'index'])->name('index');
        Route::post('/data', [SuratController::class, 'data'])->name('data');
        Route::get('/next-no-agenda', [SuratController::class, 'getNextNoAgenda'])->name('next-no-agenda');
        Route::get('/next-nomor-keluar', [SuratController::class, 'getNextNomorKeluar'])->name('next-nomor-keluar');
        Route::post('/', [SuratController::class, 'store'])->name('store');
        Route::get('/{id}', [SuratController::class, 'show'])->name('show');
        Route::post('/{id}/update', [SuratController::class, 'update'])->name('update');
        Route::delete('/{id}', [SuratController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/preview', [SuratController::class, 'preview'])->name('preview');
        Route::get('/{id}/download', [SuratController::class, 'download'])->name('download');
        Route::get('/{id}/cetak', [SuratController::class, 'cetak'])->name('cetak');
    });

    // Menu & Fitur Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/cetak', [LaporanController::class, 'cetak'])->name('cetak');
        Route::get('/pdf', [LaporanController::class, 'exportPdf'])->name('pdf');
        Route::get('/excel', [LaporanController::class, 'exportExcel'])->name('excel');
    });

    
});

require __DIR__.'/auth.php';
