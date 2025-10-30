<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parkir_slot', function (Blueprint $table) {
            $table->id('id_slot');
            $table->string('kode_slot', 20)->unique();
            $table->string('lokasi', 50);
            $table->string('status', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parkir_slot');
    }
};