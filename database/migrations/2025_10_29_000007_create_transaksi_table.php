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
        
        // --- PERBAIKAN DI SINI ---
        // Kita ubah agar mengarah ke tabel 'pengguna' (bukan 'users')
        // dan kolom referensinya 'id_pengguna'
        
        $table->unsignedBigInteger('id_pengguna');
        $table->foreign('id_pengguna')
              ->references('id_pengguna') // Nama kolom di tabel pengguna
              ->on('pengguna')            // Nama tabel (pastikan tabel 'pengguna' sudah ada)
              ->onDelete('cascade');
        // -------------------------

        // 2. Relasi ke Motor
        $table->unsignedBigInteger('id_motor');
        $table->foreign('id_motor')->references('id_motor')->on('motor')->onDelete('cascade');

        // 3. Relasi ke Petugas
        $table->unsignedBigInteger('id_petugas')->nullable();
        $table->foreign('id_petugas')->references('id_petugas')->on('petugas')->onDelete('set null');

        // 4. Relasi ke Slot Parkir
        $table->unsignedBigInteger('id_parkir_slot')->nullable();
        $table->foreign('id_parkir_slot')->references('id_parkir_slot')->on('parkir_slots')->onDelete('set null');

        $table->dateTime('jam_masuk');
        $table->dateTime('jam_keluar')->nullable();
        $table->integer('total_biaya')->default(0);
        $table->enum('status', ['Masuk', 'Selesai'])->default('Masuk');
        
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};