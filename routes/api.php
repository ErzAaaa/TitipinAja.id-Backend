<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\ParkirSlotController;
use App\Http\Controllers\MotorController;
use App\Http\Controllers\PenggunaController;

/*
|--------------------------------------------------------------------------
| API Routes (ADMIN PANEL ONLY)
|--------------------------------------------------------------------------
*/

// 1. Rute Login (Khusus Petugas)
Route::post('/login', [AuthController::class, 'login']);

// 2. Rute Protected (Perlu Token Petugas)
Route::middleware('auth:sanctum')->group(function () {

    // Auth Status
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']); // Cek siapa yg login

    // === INTI APLIKASI (Checkin/Checkout) ===
    Route::get('/dashboard', [TransaksiController::class, 'index']); // List motor aktif
    Route::post('/parkir/checkin', [TransaksiController::class, 'store']); // Input motor masuk
    Route::get('/parkir/cektiket/{kode}', [TransaksiController::class, 'cekTiket']); // Scan QR
    Route::post('/parkir/checkout', [TransaksiController::class, 'checkout']); // Bayar & Keluar

    // === MANAJEMEN DATA ===
    // (Jika Admin ingin edit manual data slot/motor)
    Route::apiResource('slots', ParkirSlotController::class);
    Route::apiResource('motors', MotorController::class);
    Route::apiResource('pengguna', PenggunaController::class);

});