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
    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string',
            'alamat'      => 'required|string',
            'no_telepon'  => 'required|string',
            'plat_nomor'  => 'required|string',
            'merk_motor'  => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // PERBAIKAN: Order By 'id_slot'
            $slot = ParkirSlot::where('status', 'Tersedia')
                        ->orderBy('id_slot', 'asc') 
                        ->first();

            if (!$slot) {
                return response()->json(['success' => false, 'message' => 'Mohon maaf, parkiran PENUH.'], 400);
            }

            $pengguna = Pengguna::firstOrCreate(
                ['no_telepon' => $request->no_telepon],
                ['nama' => $request->nama, 'alamat' => $request->alamat]
            );

            $motor = Motor::create([
                'id_pengguna' => $pengguna->id_pengguna,
                'plat_nomor'  => strtoupper($request->plat_nomor),
                'merk'        => $request->merk_motor,
                'warna'       => $request->warna ?? '-',
                'tahun'       => date('Y')
            ]);

            $kodeTiket = 'TRX-' . strtoupper(Str::random(6));

            $transaksi = Transaksi::create([
                'id_pengguna'    => $pengguna->id_pengguna,
                'id_motor'       => $motor->id_motor,
                'id_petugas'     => auth()->id(),
                
                // PERBAIKAN: Ambil ID dari $slot->id_slot
                // Pastikan kolom di tabel Transaksi bernama 'id_parkir_slot' (sesuai migration transaksi)
                'id_parkir_slot' => $slot->id_slot,
                
                'kode_tiket'     => $kodeTiket,
                'jam_masuk'      => Carbon::now(),
                'status'         => 'Masuk',
                'total_biaya'    => 0
            ]);

            $slot->update(['status' => 'Terisi']);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Check-in Berhasil',
                'data' => [
                    'tiket' => $kodeTiket,
                    'nama' => $pengguna->nama,
                    // PERBAIKAN: Gunakan nomor_slot
                    'slot' => $slot->nomor_slot ?? 'Slot ' . $slot->id_slot, 
                    'jam' => $transaksi->jam_masuk->format('H:i')
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function cekTiket($kode_tiket)
    {
        $transaksi = Transaksi::with(['motor', 'parkirSlot'])->where('kode_tiket', $kode_tiket)->first();
        if (!$transaksi) return response()->json(['success' => false, 'message' => 'Tiket tidak ditemukan.'], 404);
        if ($transaksi->status === 'Selesai') return response()->json(['success' => false, 'message' => 'Tiket sudah dibayar.'], 400);

        $jamMasuk = Carbon::parse($transaksi->jam_masuk);
        $jamKeluar = Carbon::now();
        $durasiJam = $jamMasuk->diffInHours($jamKeluar);
        if ($jamMasuk->diffInMinutes($jamKeluar) % 60 > 0) $durasiJam++;
        if ($durasiJam < 1) $durasiJam = 1;
        $totalTagihan = $durasiJam * 2000;

        return response()->json([
            'success' => true,
            'data' => [
                'kode_tiket' => $transaksi->kode_tiket,
                'plat_nomor' => $transaksi->motor->plat_nomor,
                'durasi_jam' => $durasiJam,
                'total_tagihan' => $totalTagihan,
                // PERBAIKAN: Gunakan nomor_slot
                'slot' => $transaksi->parkirSlot->nomor_slot ?? '-'
            ]
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate(['kode_tiket' => 'required', 'metode_pembayaran' => 'required']);
        DB::beginTransaction();
        try {
            $transaksi = Transaksi::where('kode_tiket', $request->kode_tiket)->first();
            if (!$transaksi || $transaksi->status == 'Selesai') return response()->json(['success' => false, 'message' => 'Transaksi tidak valid.'], 400);

            $jamMasuk = Carbon::parse($transaksi->jam_masuk);
            $jamKeluar = Carbon::now();
            $durasiJam = $jamMasuk->diffInHours($jamKeluar);
            if ($jamMasuk->diffInMinutes($jamKeluar) % 60 > 0) $durasiJam++;
            if ($durasiJam < 1) $durasiJam = 1;
            
            $transaksi->update([
                'jam_keluar' => $jamKeluar,
                'total_biaya' => $durasiJam * 2000,
                'status' => 'Selesai',
                'metode_pembayaran' => $request->metode_pembayaran
            ]);

            // PERBAIKAN: Update slot berdasarkan 'id_slot'
            if ($transaksi->id_parkir_slot) {
                ParkirSlot::where('id_slot', $transaksi->id_parkir_slot)
                    ->update(['status' => 'Tersedia']);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pembayaran Berhasil.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function index()
    {
        $data = Transaksi::with(['motor', 'parkirSlot'])->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $data]);
    }
}