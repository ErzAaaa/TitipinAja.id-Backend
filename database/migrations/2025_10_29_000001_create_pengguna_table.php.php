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
            $table->string('nama_lengkap', 100);
            
            // UBAH BARIS INI:
            // Dari: $table->int('no_telepon', 14);
            // Menjadi string agar 0 di depan tidak hilang
            $table->string('no_telepon', 20); 
            
            $table->string('alamat', 100);
            $table->string('email', 100)->unique();
            $table->string('password', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};