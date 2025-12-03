<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\ParkirSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Import library waktu

class TransaksiController extends Controller
{
    // GET - Semua Transaksi (Biasanya untuk Admin)
    public function index()
    {
        $transaksi = Transaksi::with(['motor.pengguna', 'petugas', 'tarif', 'parkirSlot'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $transaksi
        ], 200);
    }

    // POST - Check-In (Mulai Parkir)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_motor' => 'required|exists:motor,id_motor',
            // id_petugas boleh nullable jika user scan sendiri (tergantung sistem)
            'id_petugas' => 'nullable|exists:petugas,id_petugas', 
            'id_slot' => 'required|exists:parkir_slots,id_parkir_slot', // Sesuaikan nama tabel & kolom
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Cek apakah slot benar-benar tersedia
        $slot = ParkirSlot::find($request->id_slot);
        if ($slot->status !== 'Tersedia') {
            return response()->json(['success' => false, 'message' => 'Slot sudah terisi!'], 400);
        }

        DB::beginTransaction();
        try {
            // 1. Buat Data Transaksi
            $transaksi = Transaksi::create([
                'id_pengguna' => auth()->user()->id_pengguna ?? auth()->id(), // Ambil ID user yang login
                'id_motor' => $request->id_motor,
                'id_petugas' => $request->id_petugas, // Bisa null
                'id_parkir_slot' => $request->id_slot,
                'jam_masuk' => Carbon::now(), // Pakai waktu server sekarang
                'status' => 'Masuk', // Default status
                'total_biaya' => 0
            ]);

            // 2. Update Status Slot jadi 'Terisi' (Huruf Besar sesuai Enum)
            $slot->update(['status' => 'Terisi']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Check-in berhasil',
                'data' => $transaksi->load(['motor', 'parkirSlot'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal check-in',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // POST - Check-Out (Selesai Parkir & Bayar)
    public function checkout(Request $request, $id)
    {
        // Cari transaksi berdasarkan ID
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan'], 404);
        }

        if ($transaksi->status === 'Selesai') {
            return response()->json(['success' => false, 'message' => 'Transaksi ini sudah selesai'], 400);
        }

        DB::beginTransaction();
        try {
            $waktuKeluar = Carbon::now();
            $waktuMasuk = Carbon::parse($transaksi->jam_masuk);
            
            // --- LOGIKA HITUNG BIAYA ---
            // Hitung selisih jam (pembulatan ke atas)
            // Contoh: 1 jam 5 menit dianggap 2 jam
            $durasiJam = $waktuMasuk->diffInHours($waktuKeluar);
            if ($waktuMasuk->diffInMinutes($waktuKeluar) % 60 > 0) {
                $durasiJam++;
            }
            // Minimal bayar 1 jam
            if ($durasiJam < 1) $durasiJam = 1;

            $tarifPerJam = 2000; // Bisa diambil dari tabel Tarif jika ada
            $totalBiaya = $durasiJam * $tarifPerJam;

            // 1. Update Transaksi
            $transaksi->update([
                'jam_keluar' => $waktuKeluar,
                'total_biaya' => $totalBiaya,
                'status' => 'Selesai'
            ]);

            // 2. Kosongkan Slot Kembali ('Tersedia')
            if ($transaksi->id_parkir_slot) {
                ParkirSlot::where('id_parkir_slot', $transaksi->id_parkir_slot)
                    ->update(['status' => 'Tersedia']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checkout berhasil',
                'data' => $transaksi
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal checkout', 'error' => $e->getMessage()], 500);
        }
    }

    // GET - Detail Transaksi
    public function show($id)
    {
        $transaksi = Transaksi::with(['motor', 'parkirSlot'])->find($id);
        if (!$transaksi) {
            return response()->json(['success' => false, 'message' => 'Tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $transaksi], 200);
    }

    // DELETE - Hapus Transaksi (Hati-hati, slot harus dikembalikan)
    public function destroy($id)
    {
        $transaksi = Transaksi::find($id);
        if (!$transaksi) {
            return response()->json(['success' => false, 'message' => 'Tidak ditemukan'], 404);
        }

        DB::beginTransaction();
        try {
            // Jika transaksi masih aktif (belum keluar), slot harus dikosongkan dulu
            if ($transaksi->status === 'Masuk' && $transaksi->id_parkir_slot) {
                ParkirSlot::where('id_parkir_slot', $transaksi->id_parkir_slot)
                    ->update(['status' => 'Tersedia']);
            }

            $transaksi->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Data dihapus'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // GET - Transaksi Aktif (Khusus User Login)
    // Dipakai di Dashboard User untuk Timer
    public function activeTransactions()
    {
        // Filter berdasarkan ID User yang sedang login (auth()->id())
        // Dan status masih 'Masuk'
        $transaksi = Transaksi::with(['motor', 'parkirSlot'])
            ->where('id_pengguna', auth()->user()->id_pengguna ?? auth()->id()) 
            ->where('status', 'Masuk')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $transaksi
        ], 200);
    }
}