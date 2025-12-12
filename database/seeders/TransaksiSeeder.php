<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('transaksi')->insert([
            [
                'id_pengguna'    => 1, 
                'id_motor'       => 1, 
                'id_petugas'     => 1, 
                
                // PERBAIKAN: Gunakan id_parkir_slot (sesuai controller & migration)
                'id_slot'        => 1, 
                
                'kode_tiket'        => 'TRX-DUMMY1',
                'metode_pembayaran' => 'Cash',
                'jam_masuk'         => Carbon::now()->subHours(5),
                'jam_keluar'        => Carbon::now(),
                'total_biaya'       => 10000, // 5 jam * 2000
                'status'            => 'Selesai',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'id_pengguna'    => 2,
                'id_motor'       => 2, 
                'id_petugas'     => 1,
                
                // PERBAIKAN: Gunakan id_parkir_slot
                'id_slot'        => 2,
                
                'kode_tiket'        => 'TRX-DUMMY2',
                'metode_pembayaran' => null, // Belum bayar
                'jam_masuk'         => Carbon::now()->subHours(2),
                'jam_keluar'        => null, 
                'total_biaya'       => 0,
                'status'            => 'Masuk',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
        ]);

        // Opsional: Update status slot agar sesuai dengan transaksi dummy
        // Slot 2 sedang dipakai ('Masuk'), jadi statusnya harus 'Terisi'
        DB::table('parkir_slots')->where('id_slot', 2)->update(['status' => 'Terisi']);
    }
}