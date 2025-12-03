<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KartuTitipSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kartu_titip')->insert([
            [
                'id_transaksi' => 1, // Pastikan ID ini ada di TransaksiSeeder
                'nomor_kartu'  => 'KT001',
                'status'       => 'dikembalikan',
                'created_at'   => Carbon::now()->subDays(2),
                'updated_at'   => Carbon::now()->subDays(2),
            ],
            [
                'id_transaksi' => 2, // Pastikan ID ini ada di TransaksiSeeder
                'nomor_kartu'  => 'KT002',
                'status'       => 'digunakan', // Ubah status agar sesuai logika (Masuk = Digunakan)
                'created_at'   => Carbon::now()->subDays(1),
                'updated_at'   => Carbon::now()->subDays(1),
            ],
            // Data ke-3 dan ke-4 DIHAPUS dulu karena Transaksi ID 3 & 4 belum dibuat
        ]);
    }
}