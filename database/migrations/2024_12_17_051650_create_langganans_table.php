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

        Schema::create('langganan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_paket')->constrained('paket', 'id');
            $table->foreignId('id_pelanggan')->constrained('pelanggan', 'id');
            $table->string('kode_layanan', 25)->unique();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->text('alamat_lengkap')->nullable();
            $table->enum('status', ["diperiksa", "disetujui", "ditolak", "aktif", "tidak aktif"]);
            $table->text('catatan')->nullable();
            $table->date('tanggal_pemasangan')->nullable();
            $table->date('tanggal_aktif')->nullable();
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('langganan');
    }
};
