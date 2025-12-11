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
    // [FLOW 1] Check-In: Input Data -> Pilih Slot Terkecil -> Simpan
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nama'        => 'required|string',
            'alamat'      => 'required|string',
            'no_telepon'  => 'required|string',
            'plat_nomor'  => 'required|string',
            'merk_motor'  => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // 2. CARI SLOT KOSONG (DENGAN URUTAN TERKECIL)
            // orderBy('id_parkir_slot', 'asc') -> Memastikan slot A1 diambil sebelum A2
            $slot = ParkirSlot::where('status', 'Tersedia')
                        ->orderBy('id_parkir_slot', 'asc') 
                        ->first();

            if (!$slot) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Mohon maaf, parkiran PENUH.'
                ], 400);
            }

            // 3. Simpan/Cari Data Pengguna (Berdasarkan No Telp)
            $pengguna = Pengguna::firstOrCreate(
                ['no_telepon' => $request->no_telepon],
                [
                    'nama'   => $request->nama,
                    'alamat' => $request->alamat
                ]
            );

            // 4. Simpan Data Motor
            $motor = Motor::create([
                'id_pengguna' => $pengguna->id_pengguna,
                'plat_nomor'  => strtoupper($request->plat_nomor),
                'merk'        => $request->merk_motor,
                'warna'       => $request->warna ?? '-',
                'tahun'       => date('Y')
            ]);

            // 5. Generate Kode Tiket
            $kodeTiket = 'TRX-' . strtoupper(Str::random(6));

            // 6. Buat Transaksi
            $transaksi = Transaksi::create([
                'id_pengguna'    => $pengguna->id_pengguna,
                'id_motor'       => $motor->id_motor,
                'id_petugas'     => auth()->id(), // ID Petugas dari Token
                'id_parkir_slot' => $slot->id_parkir_slot,
                'kode_tiket'     => $kodeTiket,
                'jam_masuk'      => Carbon::now(),
                'status'         => 'Masuk',
                'total_biaya'    => 0
            ]);

            // 7. Update Status Slot jadi Terisi
            $slot->update(['status' => 'Terisi']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Check-in Berhasil',
                'data' => [
                    'tiket' => $kodeTiket,
                    'nama' => $pengguna->nama,
                    // Frontend akan menerima slot ini untuk ditampilkan di struk
                    'slot' => $slot->nomor_slot ?? $slot->lokasi ?? 'Slot ' . $slot->id_parkir_slot, 
                    'jam' => $transaksi->jam_masuk->format('H:i')
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // [FLOW 2] Cek Biaya (Scan Tiket)
    public function cekTiket($kode_tiket)
    {
        $transaksi = Transaksi::with(['motor', 'parkirSlot'])
            ->where('kode_tiket', $kode_tiket)
            ->first();

        if (!$transaksi) {
            return response()->json(['success' => false, 'message' => 'Tiket tidak ditemukan.'], 404);
        }
        
        if ($transaksi->status === 'Selesai') {
             return response()->json(['success' => false, 'message' => 'Tiket ini sudah dibayar sebelumnya.'], 400);
        }

        // Hitung Biaya
        $jamMasuk = Carbon::parse($transaksi->jam_masuk);
        $jamKeluar = Carbon::now();
        $durasiJam = $jamMasuk->diffInHours($jamKeluar);
        if ($jamMasuk->diffInMinutes($jamKeluar) % 60 > 0) $durasiJam++; // Pembulatan ke atas
        if ($durasiJam < 1) $durasiJam = 1;

        $tarifPerJam = 2000;
        $totalTagihan = $durasiJam * $tarifPerJam;

        return response()->json([
            'success' => true,
            'data' => [
                'kode_tiket' => $transaksi->kode_tiket,
                'plat_nomor' => $transaksi->motor->plat_nomor,
                'durasi_jam' => $durasiJam,
                'total_tagihan' => $totalTagihan,
                'slot' => $transaksi->parkirSlot->nomor_slot ?? '-'
            ]
        ]);
    }

    // [FLOW 3] Checkout & Bayar
    public function checkout(Request $request)
    {
        $request->validate([
            'kode_tiket' => 'required',
            'metode_pembayaran' => 'required'
        ]);

        DB::beginTransaction();
        try {
            $transaksi = Transaksi::where('kode_tiket', $request->kode_tiket)->first();

            if (!$transaksi || $transaksi->status == 'Selesai') {
                return response()->json(['success' => false, 'message' => 'Transaksi tidak valid.'], 400);
            }

            // Hitung Final
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
                'metode_pembayaran' => $request->metode_pembayaran
            ]);

            // KOSONGKAN SLOT (PENTING)
            if ($transaksi->id_parkir_slot) {
                ParkirSlot::where('id_parkir_slot', $transaksi->id_parkir_slot)
                    ->update(['status' => 'Tersedia']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran Berhasil. Slot telah dikosongkan.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    // Dashboard: List Transaksi Aktif
    public function index()
    {
        $data = Transaksi::with(['motor', 'parkirSlot'])
                ->orderBy('created_at', 'desc')
                ->get();
        return response()->json(['success' => true, 'data' => $data]);
    }
}