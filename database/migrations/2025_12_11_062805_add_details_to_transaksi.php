<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $table->string('kode_tiket', 20)->unique()->nullable()->after('id_transaksi');
        $table->string('metode_pembayaran', 10)->nullable()->after('total_biaya');
    }
};
