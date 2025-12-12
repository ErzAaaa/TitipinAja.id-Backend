<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Transaksi;
use App\Models\ParkirSlot; // JANGAN LUPA IMPORT INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // PERLU DB TRANSACTION
use Carbon\Carbon;

class PembayaranController extends Controller
{
    public function index()
    {
        $pembayaran = Pembayaran::with('transaksi.motor.pengguna')->get();
        return response()->json([
            'success' => true,
            'data' => $pembayaran
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_transaksi' => 'required|exists:transaksi,id_transaksi',
            // tgl_pembayaran bisa auto-generate pake Carbon::now() biar aman
            'jumlah_bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string|max:50',
            'status' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // TAMBAHAN 1: Cek Transaksi Valid & Belum Selesai
        $transaksi = Transaksi::find($request->id_transaksi);
        if ($transaksi->status == 'Selesai') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi ini sudah selesai/dibayar sebelumnya.'
            ], 400);
        }

        // GUNAKAN DB TRANSACTION AGAR DATA KONSISTEN
        DB::beginTransaction();
        try {
            // 1. Simpan Data Pembayaran
            $pembayaran = Pembayaran::create([
                'id_transaksi' => $request->id_transaksi,
                'tgl_pembayaran' => Carbon::now(), // Otomatis waktu sekarang
                'jumlah_bayar' => $request->jumlah_bayar,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status' => $request->status
            ]);

            // 2. UPDATE TRANSAKSI JADI 'SELESAI'
            $transaksi->update([
                'status' => 'Selesai',
                'jam_keluar' => Carbon::now(),
                'total_biaya' => $request->jumlah_bayar, // Sinkronkan total biaya
                'metode_pembayaran' => $request->metode_pembayaran
            ]);

            // 3. KOSONGKAN SLOT PARKIR (PENTING!)
            // Sesuai kesepakatan: kolom di transaksi 'id_slot', di slot juga 'id_slot'
            if ($transaksi->id_slot) {
                ParkirSlot::where('id_slot', $transaksi->id_slot)
                    ->update(['status' => 'Tersedia']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil. Slot telah dikosongkan.',
                'data' => $pembayaran->load('transaksi.motor.pengguna')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Gagal memproses: ' . $e->getMessage()
            ], 500);
        }
    }

    // ... (Fungsi show, update, destroy, dll biarkan tetap sama) ...
    
    public function show($id)
    {
        $pembayaran = Pembayaran::with('transaksi.motor.pengguna')->find($id);
        if (!$pembayaran) return response()->json(['success' => false, 'message' => 'Tidak ditemukan'], 404);
        return response()->json(['success' => true, 'data' => $pembayaran], 200);
    }

    public function update(Request $request, $id)
    {
        $pembayaran = Pembayaran::find($id);
        if (!$pembayaran) return response()->json(['success' => false, 'message' => 'Tidak ditemukan'], 404);
        $pembayaran->update($request->all());
        return response()->json(['success' => true, 'message' => 'Berhasil diupdate', 'data' => $pembayaran], 200);
    }

    public function destroy($id)
    {
        $pembayaran = Pembayaran::find($id);
        if (!$pembayaran) return response()->json(['success' => false, 'message' => 'Tidak ditemukan'], 404);
        $pembayaran->delete();
        return response()->json(['success' => true, 'message' => 'Berhasil dihapus'], 200);
    }

    // Method Laporan (Tetap sama)
    public function todayPayments()
    {
        $today = Carbon::today();
        $pembayaran = Pembayaran::with('transaksi.motor.pengguna')
            ->whereDate('tgl_pembayaran', $today)->get();
        return response()->json([
            'success' => true, 
            'data' => $pembayaran, 
            'total_hari_ini' => $pembayaran->sum('jumlah_bayar')
        ], 200);
    }

    public function reportByPeriod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        if ($validator->fails()) return response()->json(['success' => false, 'errors' => $validator->errors()], 422);

        $pembayaran = Pembayaran::with('transaksi.motor.pengguna')
            ->whereBetween('tgl_pembayaran', [$request->tanggal_mulai, $request->tanggal_akhir])->get();

        return response()->json([
            'success' => true,
            'periode' => ['mulai' => $request->tanggal_mulai, 'akhir' => $request->tanggal_akhir],
            'data' => $pembayaran,
            'total_pembayaran' => $pembayaran->sum('jumlah_bayar')
        ], 200);
    }
}