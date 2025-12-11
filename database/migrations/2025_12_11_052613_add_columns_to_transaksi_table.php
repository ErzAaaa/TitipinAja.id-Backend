<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('transaksi', function (Blueprint $table) {
        // Cek dulu: Jika kolom 'kode_tiket' BELUM ada, baru buat.
        if (!Schema::hasColumn('transaksi', 'kode_tiket')) {
            $table->string('kode_tiket')->nullable()->after('id_transaksi'); 
            // Sesuaikan atribut lain (unique/nullable) dengan kodingan asli Anda
        }
        
        // Lakukan hal yang sama untuk kolom lain jika ada di file ini
    });
}
};
