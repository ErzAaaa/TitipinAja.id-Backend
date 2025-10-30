<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TarifSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tarif')->insert([
            [
                'jenis_tarif' => 'Harian',
                'biaya' => 5000.00,
                'keterangan_tarif' => 'Tarif parkir untuk penggunaan harian (per hari)',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'jenis_tarif' => 'Bulanan',
                'biaya' => 100000.00,
                'keterangan_tarif' => 'Tarif parkir untuk member bulanan (30 hari)',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'jenis_tarif' => 'Per Jam',
                'biaya' => 2000.00,
                'keterangan_tarif' => 'Tarif parkir per jam (maksimal 24 jam)',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}