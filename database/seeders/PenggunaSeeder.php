<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pengguna')->insert([
            [
                'nama'          => 'Budi Santoso',      // Dulu: nama_lengkap
                'alamat'        => 'Jl. Mawar No. 12, Madiun',
                'no_telepon'    => '081234567890',
                // HAPUS email & password
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ],
            [
                'nama'          => 'Sinta Dewi',
                'alamat'        => 'Jl. Anggrek No. 8, Madiun',
                'no_telepon'    => '081298765432',
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ],
            [
                'nama'          => 'Ahmad Ridwan',
                'alamat'        => 'Jl. Melati No. 5, Madiun',
                'no_telepon'    => '081345678901',
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ],
        ]);
    }
}