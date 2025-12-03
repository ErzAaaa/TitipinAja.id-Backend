<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\ParkirSlot;
use Carbon\Carbon;

class AdminParkirController extends Controller
{
    // Fungsi untuk Admin menekan tombol "Mulai Parkir"
    public function startParkir($id_transaksi)
    {
        // 1. Cari Transaksi berdasarkan ID
        $transaksi = Transaksi::find($id_transaksi);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        // 2. Validasi: Jangan start kalau sudah berjalan
        if ($transaksi->status == 'Masuk') {
            return response()->json(['message' => 'Parkir sudah berjalan!'], 400);
        }

        // 3. SET WAKTU MASUK KE SEKARANG (Saat Admin tekan tombol)
        $waktuSekarang = Carbon::now();

        $transaksi->update([
            'waktu_masuk' => $waktuSekarang, // INI KUNCINYA
            'status'      => 'Masuk',        // Ubah status jadi aktif
        ]);

        // 4. Update status Slot jadi 'Terisi'
        if ($transaksi->id_parkir_slot) {
            ParkirSlot::where('id_parkir_slot', $transaksi->id_parkir_slot)
                ->update(['status' => 'Terisi']);
        }

        return response()->json([
            'message' => 'Waktu parkir dimulai!',
            'data'    => $transaksi
        ]);
    }
    
    // Fungsi Hitung Biaya Realtime (Opsional, untuk preview admin)
    public function cekBiaya($id_transaksi)
    {
        $transaksi = Transaksi::find($id_transaksi);
        
        if ($transaksi->status != 'Masuk') {
            return response()->json(['biaya' => 0, 'durasi' => 'Belum mulai']);
        }

        $masuk = Carbon::parse($transaksi->waktu_masuk);
        $sekarang = Carbon::now();
        
        // Hitung selisih jam
        $durasiJam = $masuk->diffInHours($sekarang);
        // Jika ada sisa menit, bulatkan ke atas (hitung 1 jam)
        if ($masuk->diffInMinutes($sekarang) % 60 > 0) {
            $durasiJam++;
        }
        
        // Contoh tarif 2000 per jam
        $biaya = ($durasiJam == 0 ? 1 : $durasiJam) * 2000;

        return response()->json([
            'durasi_jam' => $durasiJam,
            'biaya_saat_ini' => $biaya
        ]);
    }
}