<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PembayaranSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pembayaran')->insert([
            [
                'id_transaksi' => 1,
                'tgl_pembayaran' => Carbon::now()->subDays(2),
                'jumlah_bayar' => 5000.00,
                'metode_pembayaran' => 'Tunai',
                'status' => 'lunas',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'id_transaksi' => 2,
                'tgl_pembayaran' => Carbon::now()->subDays(1),
                'jumlah_bayar' => 6000.00,
                'metode_pembayaran' => 'QRIS',
                'status' => 'lunas',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
        ]);
    }
}