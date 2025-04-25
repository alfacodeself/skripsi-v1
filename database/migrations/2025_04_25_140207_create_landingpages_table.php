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
        Schema::create('landingpage', function (Blueprint $table) {
            $table->id();
            $table->string('navigasi');
            $table->string('icon_navigasi');
            $table->string('kode_navigasi')->unique();
            $table->longText('content');
            $table->integer('order')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landingpage');
    }
};
