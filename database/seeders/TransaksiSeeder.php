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
                'id_motor' => 1,
                'id_petugas' => 1,
                'id_tarif' => 1,
                'id_slot' => 1,
                'jam_masuk' => '08:00:00',
                'jam_keluar' => '17:00:00',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'id_motor' => 2,
                'id_petugas' => 2,
                'id_tarif' => 3,
                'id_slot' => 2,
                'jam_masuk' => '09:30:00',
                'jam_keluar' => '12:30:00',
                'status' => 'selesai',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'id_motor' => 3,
                'id_petugas' => 1,
                'id_tarif' => 2,
                'id_slot' => 3,
                'jam_masuk' => '10:00:00',
                'jam_keluar' => null,
                'status' => 'aktif',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_motor' => 4,
                'id_petugas' => 3,
                'id_tarif' => 3,
                'id_slot' => 4,
                'jam_masuk' => '14:00:00',
                'jam_keluar' => null,
                'status' => 'aktif',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}