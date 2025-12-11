<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek dulu apakah tabelnya ada (untuk menghindari error jika tabel terhapus)
        if (Schema::hasTable('transaksi')) {
            Schema::table('transaksi', function (Blueprint $table) {
                
                // 1. Cek apakah kolom 'kode_tiket' sudah ada? Jika BELUM, baru buat.
                if (!Schema::hasColumn('transaksi', 'kode_tiket')) {
                    $table->string('kode_tiket', 20)->unique()->nullable()->after('id_transaksi');
                }

                // 2. Cek apakah kolom 'metode_pembayaran' sudah ada? Jika BELUM, baru buat.
                if (!Schema::hasColumn('transaksi', 'metode_pembayaran')) {
                    $table->string('metode_pembayaran', 10)->nullable()->after('total_biaya');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transaksi')) {
            Schema::table('transaksi', function (Blueprint $table) {
                // Hapus kolom jika ada saat rollback
                if (Schema::hasColumn('transaksi', 'kode_tiket')) {
                    $table->dropColumn('kode_tiket');
                }
                if (Schema::hasColumn('transaksi', 'metode_pembayaran')) {
                    $table->dropColumn('metode_pembayaran');
                }
            });
        }
    }
};