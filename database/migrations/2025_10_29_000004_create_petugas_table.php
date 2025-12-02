<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petugas', function (Blueprint $table) {
            // Pastikan primary key sesuai model (id_petugas)
            $table->id('id_petugas'); 
            
            $table->string('nama_petugas');
            
            // --- TAMBAHKAN INI ---
            $table->string('email')->unique(); 
            // ---------------------
            
            // Kolom username biarkan saja (atau buat nullable) agar tidak error
            // karena controller kita tadi mengisi username otomatis pake email
            $table->string('username')->nullable(); 
            
            $table->string('password');
            $table->string('no_telepon');
            $table->string('shift_kerja');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petugas');
    }
};