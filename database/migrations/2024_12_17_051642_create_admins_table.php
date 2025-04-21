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
        Schema::create('admin', function (Blueprint $table) {
            $table->id();
            $table->string('foto')->nullable();
            $table->string('nama');
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->boolean('superadmin')->default(false);
            $table->enum('status', ["aktif", "tidak aktif", "peringatan", "diblokir"])->default('aktif');
            $table->text('catatan')->nullable();
            $table->date('tanggal_verifikasi_email')->nullable();
            $table->string('remember_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
