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

        // 3. SET WAKTU MASUK KE SEKARANG
        $waktuSekarang = Carbon::now();

        $transaksi->update([
            // PERBAIKAN: Gunakan 'jam_masuk' (bukan waktu_masuk)
            'jam_masuk' => $waktuSekarang, 
            'status'    => 'Masuk',      
        ]);

        // 4. Update status Slot jadi 'Terisi'
        // PERBAIKAN: Gunakan 'id_slot'
        if ($transaksi->id_slot) {
            // PERBAIKAN: Where ke kolom 'id_slot'
            ParkirSlot::where('id_slot', $transaksi->id_slot)
                ->update(['status' => 'Terisi']);
        }

        return response()->json([
            'message' => 'Waktu parkir dimulai!',
            'data'    => $transaksi
        ]);
    }
    
    // Fungsi Hitung Biaya Realtime
    public function cekBiaya($id_transaksi)
    {
        $transaksi = Transaksi::find($id_transaksi);
        
        if (!$transaksi || $transaksi->status != 'Masuk') {
            return response()->json(['biaya' => 0, 'durasi' => 'Belum mulai']);
        }

        // PERBAIKAN: Gunakan 'jam_masuk'
        $masuk = Carbon::parse($transaksi->jam_masuk);
        $sekarang = Carbon::now();
        
        // Hitung selisih jam
        $durasiJam = $masuk->diffInHours($sekarang);
        if ($masuk->diffInMinutes($sekarang) % 60 > 0) {
            $durasiJam++;
        }
        
        // Tarif 2000 per jam
        $biaya = ($durasiJam == 0 ? 1 : $durasiJam) * 2000;

        return response()->json([
            'durasi_jam' => $durasiJam,
            'biaya_saat_ini' => $biaya
        ]);
    }
}