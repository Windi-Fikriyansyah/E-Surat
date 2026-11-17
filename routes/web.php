<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

    // Menu Surat Masuk
    Route::prefix('surat-masuk')->name('surat-masuk.')->group(function () {
        Route::get('/', [SuratMasukController::class, 'index'])->name('index');
        Route::post('/data', [SuratMasukController::class, 'data'])->name('data');
        Route::post('/', [SuratMasukController::class, 'store'])->name('store');
        Route::get('/{id}', [SuratMasukController::class, 'show'])->name('show');
        Route::post('/{id}/update', [SuratMasukController::class, 'update'])->name('update');
        Route::delete('/{id}', [SuratMasukController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/preview', [SuratMasukController::class, 'preview'])->name('preview');
        Route::get('/{id}/download', [SuratMasukController::class, 'download'])->name('download');
    });
});

require __DIR__.'/auth.php';
