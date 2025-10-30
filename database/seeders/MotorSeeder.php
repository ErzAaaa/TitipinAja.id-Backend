<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MotorSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('motor')->insert([
            [
                'id_pengguna' => 1,
                'merk' => 'Yamaha NMAX',
                'plat_nomor' => 'AE 1234 AB',
                'warna' => 'Hitam',
                'tahun' => 2019,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pengguna' => 1,
                'merk' => 'Honda Beat',
                'plat_nomor' => 'AE 5678 CD',
                'warna' => 'Putih',
                'tahun' => 2020,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pengguna' => 2,
                'merk' => 'Honda Vario',
                'plat_nomor' => 'AE 9012 EF',
                'warna' => 'Merah',
                'tahun' => 2021,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pengguna' => 3,
                'merk' => 'Yamaha Mio',
                'plat_nomor' => 'AE 3456 GH',
                'warna' => 'Biru',
                'tahun' => 2022,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}