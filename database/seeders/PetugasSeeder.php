<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PetugasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('petugas')->insert([
            [
                'nama_petugas' => 'Admin Utama',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'no_telepon' => '081555666777',
                'shift_kerja' => 'Pagi',
                'status' => 'aktif',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_petugas' => 'Rina Petugas',
                'username' => 'rina',
                'password' => Hash::make('rina123'),
                'no_telepon' => '081666777888',
                'shift_kerja' => 'Sore',
                'status' => 'aktif',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nama_petugas' => 'Joko Security',
                'username' => 'joko',
                'password' => Hash::make('joko123'),
                'no_telepon' => '081777888999',
                'shift_kerja' => 'Malam',
                'status' => 'aktif',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}