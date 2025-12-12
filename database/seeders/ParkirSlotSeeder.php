<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ParkirSlotSeeder extends Seeder
{
    public function run(): void
    {
        // Kosongkan tabel dulu agar tidak duplikat saat seeding ulang
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('parkir_slots')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $slots = [];
        
        // Loop Lantai 1 (A1 - A10)
        for ($i = 1; $i <= 10; $i++) {
            $slots[] = [
                'nomor_slot' => 'A' . $i,
                'lokasi'     => 'Lantai 1',
                'status'     => 'Tersedia',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        // Loop Lantai 2 (B1 - B10)
        for ($i = 1; $i <= 10; $i++) {
            $slots[] = [
                'nomor_slot' => 'B' . $i,
                'lokasi'     => 'Lantai 2',
                'status'     => 'Tersedia',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        DB::table('parkir_slots')->insert($slots);
    }
}