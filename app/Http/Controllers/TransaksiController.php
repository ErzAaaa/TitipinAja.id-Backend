<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\ParkirSlot;
use App\Models\Motor;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    // Dashboard: List Transaksi yang sedang parkir (Status: Masuk)
    public function index()
    {
        $transaksi = Transaksi::with(['motor.pengguna', 'parkirSlot'])
            ->where('status', 'Masuk')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $transaksi]);
    }

    // [FLOW 1] Check-In: Petugas input data, Sistem cari slot & generate tiket
    public function store(Request $request)
    {
        // Validasi input petugas
        $request->validate([
            'plat_nomor'   => 'required|string|uppercase',
            'merk_motor'   => 'required|string',
            'nama_pemilik' => 'required|string', // Hanya nama, tidak perlu password/email user
        ]);

        DB::beginTransaction();
        try {
            // 1. Cari Slot Kosong
            $slot = ParkirSlot::where('status', 'Tersedia')->first();
            if (!$slot) {
                return response()->json(['success' => false, 'message' => 'Parkiran Penuh!'], 400);
            }

            // 2. Simpan/Cari Data Pengguna (Data Pasif)
            // Kita pakai firstOrCreate agar kalau orangnya sama tidak double data
            $pengguna = Pengguna::firstOrCreate(
                ['nama_lengkap' => $request->nama_pemilik],
                [
                    'no_telepon' => 0, // Dummy
                    'alamat' => '-',   // Dummy
                    'email' => Str::random(10).'@titipinaja.com', // Dummy agar unique key tidak error
                    'password' => bcrypt('guest'), // Dummy
                ]
            );

            // 3. Simpan Data Motor
            $motor = Motor::create([
                'id_pengguna' => $pengguna->id_pengguna,
                'plat_nomor'  => $request->plat_nomor,
                'merk'        => $request->merk_motor,
                'warna'       => $request->warna ?? '-',
                'tahun'       => date('Y')
            ]);

            // 4. Generate Kode Tiket Unik (Untuk QR Code)
            $kodeTiket = 'TRX-' . strtoupper(Str::random(6));

            // 5. Buat Transaksi
            $transaksi = Transaksi::create([
                'id_pengguna'    => $pengguna->id_pengguna,
                'id_motor'       => $motor->id_motor,
                'id_petugas'     => auth()->id(), // ID Petugas yang login
                'id_parkir_slot' => $slot->id_parkir_slot,
                'kode_tiket'     => $kodeTiket, // Pastikan sudah tambah kolom ini di migrasi
                'jam_masuk'      => Carbon::now(),
                'status'         => 'Masuk',
                'total_biaya'    => 0
            ]);

            // 6. Update Slot jadi Terisi
            $slot->update(['status' => 'Terisi']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Check-in Berhasil. Silakan cetak struk.',
                'data' => [
                    'kode_tiket' => $kodeTiket,
                    'lokasi_parkir' => $slot->lokasi . ' (' . $slot->kode_slot . ')',
                    'plat_nomor' => $motor->plat_nomor,
                    'waktu_masuk' => $transaksi->jam_masuk->format('d-m-Y H:i')
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // [FLOW 2] Scan Tiket: Cek Biaya sebelum checkout
    public function cekTiket($kode_tiket)
    {
        $transaksi = Transaksi::with(['motor', 'parkirSlot'])
            ->where('kode_tiket', $kode_tiket)
            ->where('status', 'Masuk')
            ->first();

        if (!$transaksi) {
            return response()->json(['success' => false, 'message' => 'Tiket tidak ditemukan atau sudah keluar.'], 404);
        }

        // Hitung Biaya
        $jamMasuk = Carbon::parse($transaksi->jam_masuk);
        $jamKeluar = Carbon::now();
        $durasiJam = $jamMasuk->diffInHours($jamKeluar);
        if ($jamMasuk->diffInMinutes($jamKeluar) % 60 > 0) $durasiJam++; // Pembulatan ke atas
        if ($durasiJam < 1) $durasiJam = 1;

        $tarifPerJam = 2000; // Bisa ambil dari DB Tarif
        $totalTagihan = $durasiJam * $tarifPerJam;

        return response()->json([
            'success' => true,
            'data' => [
                'kode_tiket' => $transaksi->kode_tiket,
                'plat_nomor' => $transaksi->motor->plat_nomor,
                'durasi_jam' => $durasiJam,
                'total_tagihan' => $totalTagihan,
                'metode_bayar_opsi' => ['Cash', 'QRIS']
            ]
        ]);
    }

    // [FLOW 3] Checkout: Konfirmasi Pembayaran & Buka Slot
    public function checkout(Request $request)
    {
        $request->validate([
            'kode_tiket' => 'required|exists:transaksi,kode_tiket',
            'metode_pembayaran' => 'required|in:Cash,QRIS'
        ]);

        DB::beginTransaction();
        try {
            $transaksi = Transaksi::where('kode_tiket', $request->kode_tiket)->first();

            if ($transaksi->status == 'Selesai') {
                return response()->json(['success' => false, 'message' => 'Transaksi ini sudah selesai.'], 400);
            }

            // Hitung ulang final (untuk keamanan)
            $jamMasuk = Carbon::parse($transaksi->jam_masuk);
            $jamKeluar = Carbon::now();
            $durasiJam = $jamMasuk->diffInHours($jamKeluar);
            if ($jamMasuk->diffInMinutes($jamKeluar) % 60 > 0) $durasiJam++; 
            if ($durasiJam < 1) $durasiJam = 1;
            
            $totalBiaya = $durasiJam * 2000;

            // Update Transaksi
            $transaksi->update([
                'jam_keluar' => $jamKeluar,
                'total_biaya' => $totalBiaya,
                'status' => 'Selesai',
                'metode_pembayaran' => $request->metode_pembayaran // Pastikan kolom ini ada
            ]);

            // Kosongkan Slot
            if ($transaksi->id_parkir_slot) {
                ParkirSlot::where('id_parkir_slot', $transaksi->id_parkir_slot)
                    ->update(['status' => 'Tersedia']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checkout Berhasil. Slot telah dikosongkan.',
                'data' => $transaksi
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}