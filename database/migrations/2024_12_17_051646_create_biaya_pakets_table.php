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

        Schema::create('biaya_paket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_paket')->constrained('paket', 'id');
            $table->foreignId('id_jenis_biaya')->constrained('jenis_biaya', 'id');
            $table->integer('besar_biaya');
            $table->enum('status', ["aktif", "tidak aktif"]);
            $table->text('keterangan');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_paket');
    }
};
