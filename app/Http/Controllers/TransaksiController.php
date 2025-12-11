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
    // ... import dan kode sebelumnya tetap sama ...

    public function store(Request $request)
    {
        // 1. Validasi Input Petugas
        $request->validate([
            // Data Pengguna
            'nama'        => 'required|string|max:100',
            'alamat'      => 'required|string|max:200',
            'no_telepon'  => 'required|string|max:20', // Kunci unik identitas user
            
            // Data Motor
            'plat_nomor'  => 'required|string|uppercase',
            'merk_motor'  => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // 2. Cek Slot Parkir
            $slot = ParkirSlot::where('status', 'Tersedia')->first();
            if (!$slot) {
                return response()->json(['success' => false, 'message' => 'Parkiran Penuh!'], 400);
            }

            // 3. LOGIKA PENGGUNA (Cari atau Buat Baru)
            // Kita gunakan nomor telepon sebagai patokan unik
            $pengguna = Pengguna::firstOrCreate(
                ['no_telepon' => $request->no_telepon], // Cari berdasarkan HP
                [
                    'nama' => $request->nama,           // Jika baru, isi nama
                    'alamat' => $request->alamat        // Jika baru, isi alamat
                ]
            );

            // Jika pengguna lama tapi namanya beda/update (Opsional: update data lama)
            // $pengguna->update(['nama' => $request->nama, 'alamat' => $request->alamat]);

            // 4. LOGIKA MOTOR (Cari atau Buat Baru)
            $motor = Motor::firstOrCreate(
                ['plat_nomor' => $request->plat_nomor],
                [
                    'id_pengguna' => $pengguna->id_pengguna,
                    'merk'        => $request->merk_motor,
                    'warna'       => $request->warna ?? '-',
                    'tahun'       => date('Y')
                ]
            );

            // 5. Generate Kode Tiket
            $kodeTiket = 'TRX-' . strtoupper(Str::random(6));

            // 6. Simpan Transaksi
            $transaksi = Transaksi::create([
                'id_pengguna'    => $pengguna->id_pengguna,
                'id_motor'       => $motor->id_motor,
                'id_petugas'     => auth()->id(), 
                'id_parkir_slot' => $slot->id_parkir_slot,
                'kode_tiket'     => $kodeTiket,
                'jam_masuk'      => Carbon::now(),
                'status'         => 'Masuk',
                'total_biaya'    => 0
            ]);

            // 7. Update Status Slot
            $slot->update(['status' => 'Terisi']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Check-in Berhasil',
                'data' => [
                    'tiket' => $kodeTiket,
                    'nama' => $pengguna->nama,
                    'slot' => $slot->kode_slot,
                    'jam' => $transaksi->jam_masuk->format('H:i')
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    // ... method lainnya (cekTiket, checkout) biarkan tetap sama ...

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