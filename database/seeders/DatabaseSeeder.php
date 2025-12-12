<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Master Data (Induk)
            PenggunaSeeder::class,
            PetugasSeeder::class,
            ParkirSlotSeeder::class, // Slot harus ada sebelum Transaksi
            
            // 2. Data Anak (Child)
            MotorSeeder::class,      // Motor butuh Pengguna
            MemberSeeder::class,
            
            // 3. Data Transaksi (Butuh Pengguna, Motor, Petugas, Slot)
            TransaksiSeeder::class,
            
            // 4. Lainnya
            TarifSeeder::class,
            // PembayaranSeeder::class, // Opsional jika tabel pembayaran terpisah
            // KartuTitipSeeder::class,
            // RiwayatSeeder::class,
        ]);
    }
}