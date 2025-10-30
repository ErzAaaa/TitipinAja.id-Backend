<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ParkirSlotSeeder extends Seeder
{
    public function run(): void
    {
        $slots = [];
        
        // Lantai 1 - Slot A1 sampai A10
        for ($i = 1; $i <= 10; $i++) {
            $slots[] = [
                'kode_slot' => 'A' . $i,
                'lokasi' => 'Lantai 1',
                'status' => 'kosong',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        // Lantai 2 - Slot B1 sampai B10
        for ($i = 1; $i <= 10; $i++) {
            $slots[] = [
                'kode_slot' => 'B' . $i,
                'lokasi' => 'Lantai 2',
                'status' => 'kosong',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        // Lantai 3 - Slot C1 sampai C10
        for ($i = 1; $i <= 10; $i++) {
            $slots[] = [
                'kode_slot' => 'C' . $i,
                'lokasi' => 'Lantai 3',
                'status' => 'kosong',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        DB::table('parkir_slot')->insert($slots);
    }
}