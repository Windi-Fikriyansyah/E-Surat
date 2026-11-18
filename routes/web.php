<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
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

    // Alias / redirect untuk kemudahan navigasi
    Route::get('/surat-masuk', function () {
        return redirect()->route('surat.index', ['jenis' => 'Masuk']);
    });
    Route::get('/surat-keluar', function () {
        return redirect()->route('surat.index', ['jenis' => 'Keluar']);
    });
});

require __DIR__.'/auth.php';
