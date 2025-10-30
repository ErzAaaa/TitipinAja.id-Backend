<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motor', function (Blueprint $table) {
            $table->id('id_motor');
            $table->unsignedBigInteger('id_pengguna');
            $table->string('merk', 50);
            $table->string('plat_nomor', 20);
            $table->string('warna', 30);
            $table->integer('tahun');
            $table->timestamps();

            $table->foreign('id_pengguna')
                  ->references('id_pengguna')
                  ->on('pengguna')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motor');
    }
};