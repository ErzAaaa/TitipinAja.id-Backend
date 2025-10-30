<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarif', function (Blueprint $table) {
            $table->id('id_tarif');
            $table->string('jenis_tarif', 50);
            $table->decimal('biaya', 10, 2);
            $table->text('keterangan_tarif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarif');
    }
};