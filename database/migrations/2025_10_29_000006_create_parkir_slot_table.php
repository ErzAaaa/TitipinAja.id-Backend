<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PERBAIKAN: Gunakan 'parkir_slots' (Jamak/Plural)
        // Agar sesuai dengan standar Laravel dan referensi di tabel transaksi
        Schema::create('parkir_slots', function (Blueprint $table) {
            
            $table->id('id_slot'); 
            $table->string('nomor_slot', 20)->unique(); 
            $table->string('lokasi', 50);
            $table->enum('status', ['Tersedia', 'Terisi'])->default('Tersedia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parkir_slots');
    }
};