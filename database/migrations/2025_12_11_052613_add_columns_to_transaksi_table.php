<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('transaksi', function (Blueprint $table) {
        // Cek dulu, kalau kolom kode_tiket BELUM ada, baru buat
        if (!Schema::hasColumn('transaksi', 'kode_tiket')) {
            // Sesuaikan atribut ini dengan codingan asli di file tersebut
            $table->string('kode_tiket')->nullable()->after('id_transaksi'); 
        }

        // Cek juga kolom lain jika ada di file ini, misal metode_pembayaran
        if (!Schema::hasColumn('transaksi', 'metode_pembayaran')) {
            $table->string('metode_pembayaran')->nullable()->after('total_biaya');
        }
    });
}
};
