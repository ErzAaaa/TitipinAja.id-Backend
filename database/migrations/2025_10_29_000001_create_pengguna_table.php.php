<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id('id_pengguna');
            $table->string('nama', 100);       // Mengganti nama_lengkap -> nama
            $table->string('alamat', 200)->nullable(); // Alamat opsional
            $table->string('no_telepon', 20);  // String agar angka 0 di depan tidak hilang
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};