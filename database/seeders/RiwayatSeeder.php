<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RiwayatSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('riwayat')->insert([
            [
                'id_transaksi' => 1, // Valid: ID ini ada
                'id_pengguna'  => 1, // Pastikan User ID 1 ada
                'keterangan'   => 'Parkir motor Yamaha NMAX (AE 1234 AB) masuk pukul 08:00, keluar pukul 17:00. Pembayaran tunai Rp 5.000',
                'created_at'   => Carbon::now()->subDays(2),
                'updated_at'   => Carbon::now()->subDays(2),
            ],
            [
                'id_transaksi' => 2, // Valid: ID ini ada
                'id_pengguna'  => 1,
                'keterangan'   => 'Parkir motor Honda Beat (AE 5678 CD) masuk pukul 09:30, keluar pukul 12:30. Pembayaran QRIS Rp 6.000',
                'created_at'   => Carbon::now()->subDays(1),
                'updated_at'   => Carbon::now()->subDays(1),
            ],
            // Data ID 3 dan 4 DIHAPUS karena transaksinya belum dibuat
        ]);
    }
}