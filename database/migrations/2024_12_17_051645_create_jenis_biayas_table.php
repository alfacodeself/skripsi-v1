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

        Schema::create('jenis_biaya', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis_biaya', ["persentase", "flat"]);
            $table->boolean('berulang')->default(true);
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
        Schema::dropIfExists('jenis_biaya');
    }
};
