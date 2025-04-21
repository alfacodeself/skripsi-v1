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

        Schema::create('jenis_dokumen', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dokumen', 150);
            $table->string('path_dokumen', 200);
            $table->string('contoh_dokumen');
            $table->longText('deskripsi_dokumen');
            $table->enum('status', ["aktif", "tidak aktif"]);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_dokumen');
    }
};
