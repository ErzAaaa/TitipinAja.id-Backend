<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('transaksi', function (Blueprint $table) {
        $table->string('kode_tiket')->unique()->after('id_transaksi'); // Kode unik untuk QR
        $table->string('metode_pembayaran')->nullable()->after('total_biaya'); // Cash / QRIS
    });
}
};
