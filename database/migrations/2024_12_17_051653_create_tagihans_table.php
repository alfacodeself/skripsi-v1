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

        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_langganan')->constrained('langganan', 'id');
            $table->integer('total_tagihan');
            $table->integer('sisa_tagihan');
            $table->date('jatuh_tempo');
            $table->boolean('status_angsuran');
            $table->integer('jumlah_angsuran')->nullable();
            $table->enum('status', ["lunas", "belum lunas", "dalam angsuran"]);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
