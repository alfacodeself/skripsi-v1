<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('detail_tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tagihan')->constrained('tagihan', 'id');
            $table->foreignId('id_biaya_paket')->constrained('biaya_paket', 'id');
            $table->integer('jumlah_biaya');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_tagihan');
    }
};
