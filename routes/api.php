<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- IMPORT SEMUA CONTROLLER ANDA DI SINI ---
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\MotorController;     // <--- WAJIB: Untuk data motor
use App\Http\Controllers\TransaksiController; // <--- WAJIB: Untuk transaksi parkir
use App\Http\Controllers\TarifController;     // <--- WAJIB: Untuk setting harga

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. RUTE PUBLIK (Bisa diakses tanpa login)
// ==========================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// ==========================================
// 2. RUTE PRIVATE (Harus Login & Punya Token)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {

    // --- AUTH & PROFILE ---
    Route::post('/logout', [AuthController::class, 'logout']); 
    // Route profile ini penting agar Flutter tahu siapa yang sedang login
    Route::get('/profile', [PenggunaController::class, 'profile']); 


    // --- DATA PENGGUNA & PETUGAS (User Management) ---
    // 'apiResource' otomatis membuat route: index, store, show, update, destroy
    Route::apiResource('pengguna', PenggunaController::class);
    Route::apiResource('petugas', PetugasController::class);


    // --- DATA INTI APLIKASI (Motor & Tarif) ---
    Route::apiResource('motor', MotorController::class); 
    Route::apiResource('tarif', TarifController::class);


    // --- TRANSAKSI & RIWAYAT ---
    Route::apiResource('transaksi', TransaksiController::class);
    
    // Rute Custom untuk Riwayat/Aktivitas
    Route::get('/aktivitas', [RiwayatController::class, 'index']);

    Route::post('/parkir/checkin', [TransaksiController::class, 'checkIn']);
    
    // 2. Cek Status Aktif (Untuk Dashboard)
    Route::get('/parkir/aktivitas', [TransaksiController::class, 'getActiveTransaction']);
    
    // 3. Selesai Parkir (Hitung Biaya & Keluar)
    Route::post('/parkir/checkout', [TransaksiController::class, 'checkOut']);

    
});