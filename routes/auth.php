<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
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
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
