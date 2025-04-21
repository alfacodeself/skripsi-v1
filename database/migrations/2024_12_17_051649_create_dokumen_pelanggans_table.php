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

        Schema::create('dokumen_pelanggan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_dokumen')->constrained('jenis_dokumen', 'id');
            $table->foreignId('id_pelanggan')->constrained('pelanggan', 'id');
            $table->string('path');
            $table->enum('status', ["diperiksa", "disetujui", "ditolak"]);
            $table->boolean('akses_pelanggan')->default(false);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_pelanggan');
    }
};
