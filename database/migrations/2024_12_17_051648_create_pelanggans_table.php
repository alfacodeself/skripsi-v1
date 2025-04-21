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

        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id();
            $table->string('foto')->nullable();
            $table->string('nama');
            $table->string('kode', 20)->unique();
            $table->string('email', 150)->unique();
            $table->string('telepon', 25)->unique();
            $table->string('password');
            $table->enum('status', ["aktif", "tidak aktif", "peringatan", "diblokir", "diperiksa"])->default('aktif');
            $table->text('catatan')->nullable();
            $table->date('tanggal_verifikasi_email')->nullable();
            $table->date('tanggal_verifikasi_telepon')->nullable();
            $table->string('remember_token')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggan');
    }
};
