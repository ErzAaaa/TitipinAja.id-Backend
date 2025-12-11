<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\ParkirSlot;
use App\Models\Motor;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    // ==========================================================
    // 1. CHECK-IN (Dilakukan oleh Petugas)
    // ==========================================================
    public function store(Request $request)
    {
        // Petugas menginput data pengguna dan motor di tempat
        $validator = Validator::make($request->all(), [
            'plat_nomor'   => 'required|string|uppercase',
            'merk_motor'   => 'required|string',
            'nama_pemilik' => 'required|string',
            // Opsional: No HP atau Identitas lain jika diperlukan
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // A. Cari Slot Parkir yang Kosong Secara Otomatis
            $slot = ParkirSlot::where('status', 'Tersedia')->first();
            
            if (!$slot) {
                return response()->json(['success' => false, 'message' => 'Mohon maaf, parkiran penuh.'], 400);
            }

            // B. Handle Data Pengguna (Cek jika sudah ada, atau buat baru)
            // Disini kita asumsikan 'nama_pemilik' cukup, tapi idealnya pakai No HP/KTP agar unik
            $pengguna = Pengguna::firstOrCreate(
                ['nama_lengkap' => $request->nama_pemilik],
                [
                    'password' => bcrypt('123456'), // Default password dummy
                    'email' => Str::random(10).'@guest.com', // Email dummy jika tidak diminta
                    'no_telepon' => 0, 
                    'alamat' => '-'
                ]
            );

            // C. Handle Data Motor
            $motor = Motor::firstOrCreate(
                ['plat_nomor' => $request->plat_nomor],
                [
                    'id_pengguna' => $pengguna->id_pengguna,
                    'merk' => $request->merk_motor,
                    'warna' => $request->warna ?? '-',
                    'tahun' => date('Y')
                ]
            );

            // D. Generate Kode Unik untuk Struk/QR
            // Format: TRX-TAHUNBULANTANGGAL-RANDOM (Contoh: TRX-20251029-X7Z)
            $kodeTiket = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            // E. Buat Transaksi
            $transaksi = Transaksi::create([
                'id_pengguna'    => $pengguna->id_pengguna,
                'id_motor'       => $motor->id_motor,
                'id_petugas'     => auth()->id(), // ID Petugas yang sedang login
                'id_parkir_slot' => $slot->id_parkir_slot,
                'kode_tiket'     => $kodeTiket,
                'jam_masuk'      => Carbon::now(),
                'status'         => 'Masuk',
                'total_biaya'    => 0
            ]);

            // F. Update Status Slot jadi Terisi
            $slot->update(['status' => 'Terisi']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Check-in berhasil. Silakan cetak struk.',
                'data' => [
                    'kode_tiket' => $transaksi->kode_tiket, // Ini dijadikan QR Code
                    'slot_parkir' => $slot->lokasi . ' - ' . $slot->kode_slot, // Arahkan user ke sini
                    'jam_masuk' => $transaksi->jam_masuk->format('Y-m-d H:i:s'),
                    'nama_pengguna' => $pengguna->nama_lengkap,
                    'plat_nomor' => $motor->plat_nomor
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal check-in', 'error' => $e->getMessage()], 500);
        }
    }

    // ==========================================================
    // 2. CEK BIAYA SEBELUM BAYAR (Scan QR)
    // ==========================================================
    public function cekBiaya(Request $request)
    {
        // Petugas scan QR -> Masuk kode_tiket
        $validator = Validator::make($request->all(), [
            'kode_tiket' => 'required|exists:transaksi,kode_tiket'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Kode tiket tidak ditemukan'], 404);
        }

        $transaksi = Transaksi::with(['motor', 'parkirSlot'])
                        ->where('kode_tiket', $request->kode_tiket)
                        ->first();

        if ($transaksi->status === 'Selesai') {
            return response()->json(['success' => false, 'message' => 'Transaksi ini sudah selesai sebelumnya'], 400);
        }

        // Hitung Biaya Realtime
        $biaya = $this->hitungBiaya($transaksi->jam_masuk);

        return response()->json([
            'success' => true,
            'data' => [
                'id_transaksi' => $transaksi->id_transaksi,
                'kode_tiket' => $transaksi->kode_tiket,
                'plat_nomor' => $transaksi->motor->plat_nomor,
                'durasi' => $biaya['durasi_jam'] . ' Jam',
                'total_biaya' => $biaya['total']
            ]
        ]);
    }

    // ==========================================================
    // 3. CHECK-OUT & BAYAR (Verifikasi Akhir)
    // ==========================================================
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_tiket' => 'required|exists:transaksi,kode_tiket',
            'metode_pembayaran' => 'required|in:Cash,QRIS'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $transaksi = Transaksi::where('kode_tiket', $request->kode_tiket)->first();

            // Pastikan belum keluar
            if ($transaksi->status === 'Selesai') {
                return response()->json(['success' => false, 'message' => 'Kendaraan sudah checkout sebelumnya'], 400);
            }

            // Hitung Final Biaya
            $biaya = $this->hitungBiaya($transaksi->jam_masuk);

            // Update Transaksi
            $transaksi->update([
                'jam_keluar' => Carbon::now(),
                'total_biaya' => $biaya['total'],
                'metode_pembayaran' => $request->metode_pembayaran,
                'status' => 'Selesai'
            ]);

            // Kosongkan Slot (PENTING)
            if ($transaksi->id_parkir_slot) {
                ParkirSlot::where('id_parkir_slot', $transaksi->id_parkir_slot)
                    ->update(['status' => 'Tersedia']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil. Slot telah dikosongkan.',
                'data' => $transaksi
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // Helper Hitung Biaya
    private function hitungBiaya($jamMasuk)
    {
        $masuk = Carbon::parse($jamMasuk);
        $keluar = Carbon::now();
        
        $durasiJam = $masuk->diffInHours($keluar);
        // Jika lebih 1 menit, anggap masuk jam berikutnya
        if ($masuk->diffInMinutes($keluar) % 60 > 0) {
            $durasiJam++;
        }
        if ($durasiJam < 1) $durasiJam = 1; // Minimal 1 jam

        $tarifPerJam = 2000; // Saran: Ambil dari database Tarif
        
        return [
            'durasi_jam' => $durasiJam,
            'total' => $durasiJam * $tarifPerJam
        ];
    }
}