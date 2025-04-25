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
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_langganan')->constrained('langganan', 'id');
            $table->foreignId('id_kategori_pengaduan')->constrained('kategori_pengaduan', 'id');
            $table->text('deskripsi');
            $table->string('lampiran')->nullable();
            $table->text('tanggapan')->nullable();
            $table->enum('status', ['menunggu', 'diproses', 'ditolak', 'selesai']);
            $table->foreignId('ditangani_oleh')->nullable()->constrained('admin', 'id');
            $table->dateTime('tanggal_selesai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};
