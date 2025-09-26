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
        Schema::create('master_sub_disiplin', function (Blueprint $table) {
            $table->id('id_sub_disiplin');
            $table->text('remark')->nullable();
            $table->foreignId('id_disiplin')->constrained('master_disiplin', 'id_disiplin')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_sub_disiplin');
    }
};
