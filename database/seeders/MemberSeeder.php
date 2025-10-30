<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('member')->insert([
            [
                'id_pengguna' => 1,
                'tanggal_daftar' => Carbon::now()->subDays(30),
                'jenis_member' => 'Bulanan',
                'diskon_decimal' => 10.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id_pengguna' => 2,
                'tanggal_daftar' => Carbon::now()->subDays(15),
                'jenis_member' => 'Harian',
                'diskon_decimal' => 5.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}