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
        Schema::create('eps', function (Blueprint $table) {
            $table->id('id_eps');
            $table->text('remark')->nullable();
            $table->date('execution_date')->nullable();
            $table->integer('default')->default(0);
            $table->integer('tahun');
            $table->string('jenis_project');
            $table->string('cutoff_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eps');
    }
};
