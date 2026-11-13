<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Support\Facades\Route;

/*
 * Pendaftaran publik sengaja tidak didaftarkan (route register dihapus).
 * Akun Admin hanya boleh dibuat oleh Super Admin lewat panel, dan akun
 * Super Admin hanya lewat seeder — lihat Features-CMS-Admin.md bagian 4.
 */
Route::middleware('guest')->group(function () {
    /*
     * Login admin sengaja ditempatkan di /admin/login (bukan /login) agar
     * pintu masuk panel tidak mudah ditebak dari halaman publik.
     * Nama route tetap `login` supaya middleware `auth` & tautan internal
     * tidak perlu diubah.
     */
    Route::get('admin/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('admin/login', [AuthenticatedSessionController::class, 'store']);

    /*
     * Reset password tidak tersedia lewat halaman publik.
     * Super Admin mereset password akun admin dari panel Manajemen Admin.
     */
});

Route::middleware('auth')->group(function () {
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
