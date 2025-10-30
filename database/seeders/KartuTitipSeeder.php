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
                'id_transaksi' => 1,
                'nomor_kartu' => 'KT001',
                'status' => 'dikembalikan',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'id_transaksi' => 2,
                'nomor_kartu' => 'KT002',
                'status' => 'dikembalikan',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'id_transaksi' => 3,
                'nomor_kartu' => 'KT003',
                'status' => 'digunakan',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_transaksi' => 4,
                'nomor_kartu' => 'KT004',
                'status' => 'digunakan',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}