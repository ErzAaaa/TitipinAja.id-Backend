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
        
        // Loop Lantai 1 (A1 - A10)
        for ($i = 1; $i <= 10; $i++) {
            $slots[] = [
                'nomor_slot' => 'A' . $i,       // Perbaikan: gunakan 'nomor_slot'
                'lokasi'     => 'Lantai 1',
                'status'     => 'Tersedia',     // Perbaikan: gunakan 'Tersedia'
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
        
        // Loop Lantai 3 (C1 - C10)
        for ($i = 1; $i <= 10; $i++) {
            $slots[] = [
                'nomor_slot' => 'C' . $i,
                'lokasi'     => 'Lantai 3',
                'status'     => 'Tersedia',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        // PENTING: Masukkan ke tabel 'parkir_slots' (ada 's' di belakang)
        DB::table('parkir_slots')->insert($slots);
    }
}