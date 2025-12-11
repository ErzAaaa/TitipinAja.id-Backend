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
                'id_parkir_slot' => 1, 
                
                // TAMBAHKAN DUA BARIS INI:
                'kode_tiket'     => 'TICKET-001', // Beri nilai unik (sesuai constraint di DB)
                'metode_pembayaran' => 'Cash',   // Isi sesuai kebutuhan data dummy
                
                'jam_masuk'      => Carbon::now()->subHours(5),
                'jam_keluar'     => Carbon::now(),
                'total_biaya'    => 5000,
                'status'         => 'Selesai',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ],
            [
                'id_pengguna'    => 2,
                'id_motor'       => 2, 
                'id_petugas'     => 1,
                'id_parkir_slot' => 2,
                
                // TAMBAHKAN DUA BARIS INI:
                'kode_tiket'     => 'TICKET-002', // Beri nilai unik
                'metode_pembayaran' => null,   // Bisa null karena belum selesai/belum bayar
                
                'jam_masuk'      => Carbon::now()->subHours(2),
                'jam_keluar'     => null, 
                'total_biaya'    => 0,
                'status'         => 'Masuk',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ],
        ]);
    }
}