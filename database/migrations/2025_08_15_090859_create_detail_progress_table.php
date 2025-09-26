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
        Schema::create('detail_progress', function (Blueprint $table) {
            $table->id('id_detail_progress');
            $table->integer('id_tamu')->nullable();
            $table->foreignId('id_kategori')->constrained('master_tahapan');
            $table->date('plan_start')->nullable();
            $table->date('plan_finish')->nullable();
            $table->date('actual_start')->nullable();
            $table->date('actual_finish')->nullable();
            $table->integer('plan_progress')->nullable();
            $table->integer('actual_progress')->nullable();
            $table->date('early_start')->nullable();
            $table->date('early_finish')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_progress');
    }
};
