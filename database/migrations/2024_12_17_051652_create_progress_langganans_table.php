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

        Schema::create('progress_langganan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_progress')->constrained('progress', 'id');
            $table->foreignId('id_langganan')->constrained('langganan', 'id');
            $table->enum('status', ["diproses", "selesai", "gagal", "menunggu"])->default('menunggu');
            $table->string('bukti')->nullable();
            $table->text('keterangan')->nullable();
            $table->date('tanggal_perencanaan')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_langganan');
    }
};
