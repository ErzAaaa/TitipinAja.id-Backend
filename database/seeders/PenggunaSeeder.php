<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pengguna')->insert([
            [
                'nama_lengkap' => 'Budi Santoso',
                'alamat' => 'Jl. Mawar No. 12, Madiun',
                'no_telepon' => '081234567890',
                'email' => 'budi@example.com',
                'password' => Hash::make('123456'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_lengkap' => 'Sinta Dewi',
                'alamat' => 'Jl. Anggrek No. 8, Madiun',
                'no_telepon' => '081298765432',
                'email' => 'sinta@example.com',
                'password' => Hash::make('654321'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_lengkap' => 'Ahmad Ridwan',
                'alamat' => 'Jl. Melati No. 5, Madiun',
                'no_telepon' => '081345678901',
                'email' => 'ahmad@example.com',
                'password' => Hash::make('password123'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}