<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\RiwayatController; // <-- TAMBAHKAN IMPORT INI

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rute Publik (Auth)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rute Terproteksi (CRUD Pengguna, Petugas, dan Aktivitas)
Route::middleware('auth:sanctum')->group(function () {
    
    // Rute Logout
    Route::post('/logout', [AuthController::class, 'logout']); // <-- Saya tambahkan rute logout

    // Pengguna
    Route::get('/pengguna', [PenggunaController::class, 'index']);
    Route::get('/pengguna/{id}', [PenggunaController::class, 'show']);
    Route::put('/pengguna/{id}', [PenggunaController::class, 'update']);
    Route::delete('/pengguna/{id}', [PenggunaController::class, 'destroy']);
    
    // Petugas
    Route::get('/petugas', [PetugasController::class, 'index']);
    Route::get('/petugas/{id}', [PetugasController::class, 'show']);
    Route::post('/petugas', [PetugasController::class, 'store']); // Tambahkan rute store
    Route::put('/petugas/{id}', [PetugasController::class, 'update']);
    Route::delete('/petugas/{id}', [PetugasController::class, 'destroy']);

    // --- INI PERBAIKANNYA ---
    // Rute untuk "Aktivitas Terbaru" di dashboard Anda
    // Pastikan Anda memiliki RiwayatController
    Route::get('/aktivitas', [RiwayatController::class, 'index']);
});

