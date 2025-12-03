<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ID referensi (id_pengguna, id_motor, dll) benar-benar ada di tabel masing-masing
        // atau Seeder akan error "Foreign Key Constraint Fails"
        
        DB::table('transaksi')->insert([
            [
                'id_pengguna'    => 1, // WAJIB ADA: Pastikan tabel 'pengguna' punya id 1
                'id_motor'       => 1, // Pastikan tabel 'motor' punya id 1
                'id_petugas'     => 1, // Pastikan tabel 'petugas' punya id 1
                'id_parkir_slot' => 1, // Pastikan tabel 'parkir_slots' punya id 1
                // 'id_tarif'    => 1, // HAPUS INI (Karena tidak ada di migration)
                
                'jam_masuk'      => Carbon::now()->subHours(5),
                'jam_keluar'     => Carbon::now(),
                'total_biaya'    => 5000,
                'status'         => 'Selesai',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ],
            [
                'id_pengguna'    => 2,
                'id_motor'       => 2, 
                'id_petugas'     => 1,
                'id_parkir_slot' => 2,
                
                'jam_masuk'      => Carbon::now()->subHours(2),
                'jam_keluar'     => null, // Masih parkir
                'total_biaya'    => 0,
                'status'         => 'Masuk',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ],
        ]);
    }
}