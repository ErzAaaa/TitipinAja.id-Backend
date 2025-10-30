<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PenggunaSeeder::class,
            MemberSeeder::class,
            PetugasSeeder::class,
            TarifSeeder::class,
            ParkirSlotSeeder::class,
            MotorSeeder::class,
            TransaksiSeeder::class,
            PembayaranSeeder::class,
            KartuTitipSeeder::class,
            RiwayatSeeder::class,
        ]);
    }
}