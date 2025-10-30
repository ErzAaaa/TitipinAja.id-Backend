<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kartu_titip', function (Blueprint $table) {
            $table->id('id_kartu');
            $table->unsignedBigInteger('id_transaksi');
            $table->string('nomor_kartu', 50)->unique();
            $table->string('status', 25);
            $table->timestamps();

            $table->foreign('id_transaksi')
                  ->references('id_transaksi')
                  ->on('transaksi')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kartu_titip');
    }
};