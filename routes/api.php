<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MotorController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\ParkirSlotController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\KartuTitipController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\AuthController;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ========================================
// PENGGUNA ROUTES
// ========================================
Route::prefix('pengguna')->group(function () {
    Route::get('/', [PenggunaController::class, 'index']); // GET all pengguna
    Route::post('/', [PenggunaController::class, 'store']); // POST create pengguna
    Route::get('/{id}', [PenggunaController::class, 'show']); // GET single pengguna
    Route::put('/{id}', [PenggunaController::class, 'update']); // PUT update pengguna
    Route::delete('/{id}', [PenggunaController::class, 'destroy']); // DELETE pengguna
});

// ========================================
// MEMBER ROUTES
// ========================================
Route::prefix('member')->group(function () {
    Route::get('/', [MemberController::class, 'index']);
    Route::post('/', [MemberController::class, 'store']);
    Route::get('/{id}', [MemberController::class, 'show']);
    Route::put('/{id}', [MemberController::class, 'update']);
    Route::delete('/{id}', [MemberController::class, 'destroy']);
});

// ========================================
// MOTOR ROUTES
// ========================================
Route::prefix('motor')->group(function () {
    Route::get('/', [MotorController::class, 'index']);
    Route::post('/', [MotorController::class, 'store']);
    Route::get('/{id}', [MotorController::class, 'show']);
    Route::put('/{id}', [MotorController::class, 'update']);
    Route::delete('/{id}', [MotorController::class, 'destroy']);
});

// ========================================
// PETUGAS ROUTES
// ========================================
Route::prefix('petugas')->group(function () {
    Route::get('/', [PetugasController::class, 'index']);
    Route::post('/', [PetugasController::class, 'store']);
    Route::get('/{id}', [PetugasController::class, 'show']);
    Route::put('/{id}', [PetugasController::class, 'update']);
    Route::delete('/{id}', [PetugasController::class, 'destroy']);
});

// ========================================
// TARIF ROUTES
// ========================================
Route::prefix('tarif')->group(function () {
    Route::get('/', [TarifController::class, 'index']);
    Route::post('/', [TarifController::class, 'store']);
    Route::get('/{id}', [TarifController::class, 'show']);
    Route::put('/{id}', [TarifController::class, 'update']);
    Route::delete('/{id}', [TarifController::class, 'destroy']);
});

// ========================================
// PARKIR SLOT ROUTES
// ========================================
Route::prefix('parkir-slot')->group(function () {
    Route::get('/', [ParkirSlotController::class, 'index']);
    Route::post('/', [ParkirSlotController::class, 'store']);
    Route::get('/available', [ParkirSlotController::class, 'availableSlots']); // Get slot kosong
    Route::get('/{id}', [ParkirSlotController::class, 'show']);
    Route::put('/{id}', [ParkirSlotController::class, 'update']);
    Route::delete('/{id}', [ParkirSlotController::class, 'destroy']);
});

// ========================================
// TRANSAKSI ROUTES
// ========================================
Route::prefix('transaksi')->group(function () {
    Route::get('/', [TransaksiController::class, 'index']);
    Route::post('/', [TransaksiController::class, 'store']);
    Route::get('/active', [TransaksiController::class, 'activeTransactions']); // Get transaksi aktif
    Route::get('/{id}', [TransaksiController::class, 'show']);
    Route::put('/{id}', [TransaksiController::class, 'update']);
    Route::post('/{id}/checkout', [TransaksiController::class, 'checkout']); // Checkout (keluar parkir)
    Route::delete('/{id}', [TransaksiController::class, 'destroy']);
});

// ========================================
// PEMBAYARAN ROUTES
// ========================================
Route::prefix('pembayaran')->group(function () {
    Route::get('/', [PembayaranController::class, 'index']);
    Route::post('/', [PembayaranController::class, 'store']);
    Route::get('/today', [PembayaranController::class, 'todayPayments']); // Laporan hari ini
    Route::post('/report', [PembayaranController::class, 'reportByPeriod']); // Laporan by periode
    Route::get('/{id}', [PembayaranController::class, 'show']);
    Route::put('/{id}', [PembayaranController::class, 'update']);
    Route::delete('/{id}', [PembayaranController::class, 'destroy']);
});

// ========================================
// KARTU TITIP ROUTES
// ========================================
Route::prefix('kartu-titip')->group(function () {
    Route::get('/', [KartuTitipController::class, 'index']);
    Route::post('/', [KartuTitipController::class, 'store']);
    Route::get('/active', [KartuTitipController::class, 'activeCards']); // Kartu yang sedang digunakan
    Route::get('/available', [KartuTitipController::class, 'availableCards']); // Kartu tersedia
    Route::get('/number/{nomor_kartu}', [KartuTitipController::class, 'findByNumber']); // Cari by nomor
    Route::get('/{id}', [KartuTitipController::class, 'show']);
    Route::put('/{id}', [KartuTitipController::class, 'update']);
    Route::delete('/{id}', [KartuTitipController::class, 'destroy']);
});

// ========================================
// RIWAYAT ROUTES
// ========================================
Route::prefix('riwayat')->group(function () {
    Route::get('/', [RiwayatController::class, 'index']);
    Route::post('/', [RiwayatController::class, 'store']);
    Route::get('/user/{id_pengguna}', [RiwayatController::class, 'byUser']); // Riwayat by user
    Route::get('/transaction/{id_transaksi}', [RiwayatController::class, 'byTransaction']); // Riwayat by transaksi
    Route::get('/{id}', [RiwayatController::class, 'show']);
    Route::put('/{id}', [RiwayatController::class, 'update']);
    Route::delete('/{id}', [RiwayatController::class, 'destroy']);
});