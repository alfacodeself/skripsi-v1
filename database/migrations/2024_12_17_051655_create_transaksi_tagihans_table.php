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

        Schema::create('transaksi_tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tagihan')->constrained('tagihan', 'id');
            $table->string('kode', 150)->unique();
            $table->integer('jumlah_bayar');
            $table->enum('metode_pembayaran', ['midtrans', 'cash']);
            $table->string('bukti_pembayaran')->nullable();
            $table->json('midtrans_response')->nullable();
            $table->text('keterangan')->nullable();
            $table->dateTime('tanggal_lunas')->nullable();
            $table->dateTime('tanggal_kadaluarsa')->nullable();
            $table->enum('status', [
                'menunggu',
                'diperiksa',
                'dibatalkan',
                'kadaluarsa',
                'lunas',
                'gagal',
            ])->default('menunggu');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_tagihan');
    }
};
