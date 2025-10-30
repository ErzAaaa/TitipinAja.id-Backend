<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->unsignedBigInteger('id_motor');
            $table->unsignedBigInteger('id_petugas');
            $table->unsignedBigInteger('id_tarif');
            $table->unsignedBigInteger('id_slot')->nullable();
            $table->time('jam_masuk');
            $table->time('jam_keluar')->nullable();
            $table->string('status', 20);
            $table->timestamps();

            $table->foreign('id_motor')
                  ->references('id_motor')
                  ->on('motor')
                  ->onDelete('cascade');
            
            $table->foreign('id_petugas')
                  ->references('id_petugas')
                  ->on('petugas')
                  ->onDelete('cascade');
            
            $table->foreign('id_tarif')
                  ->references('id_tarif')
                  ->on('tarif')
                  ->onDelete('cascade');
            
            $table->foreign('id_slot')
                  ->references('id_slot')
                  ->on('parkir_slot')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};