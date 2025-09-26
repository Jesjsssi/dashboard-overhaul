<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jasa', function (Blueprint $table) {
            $table->id('id_jasa');
            $table->integer('id_eps');
            $table->string('kode_jasa');
            $table->string('judul_kontrak');
            $table->integer('id_disiplin')->nullable();
            $table->string('planner')->nullable();
            $table->string('wo')->nullable();
            $table->string('pr')->nullable();
            $table->string('po')->nullable();
            $table->string('pemenang')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jasa');
    }
};
