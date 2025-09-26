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
        Schema::create('project', function (Blueprint $table) {
            $table->id();
            $table->integer('id_eps');
            $table->string('kode_rkap')->nullable();
            $table->integer('id_disiplin');
            $table->integer('id_sub_disiplin')->nullable();
            $table->string('tagno');
            $table->text('remark')->nullable();
            $table->float('weight_factor');
            
            // Add the 15 step date fields
            $table->date('step_1_date')->nullable();
            $table->date('step_2_date')->nullable();
            $table->date('step_3_date')->nullable();
            $table->date('step_4_date')->nullable();
            $table->date('step_5_date')->nullable();
            $table->date('step_6_date')->nullable();
            $table->date('step_7_date')->nullable();
            $table->date('step_8_date')->nullable();
            $table->date('step_9_date')->nullable();
            $table->date('step_10_date')->nullable();
            $table->date('step_11_date')->nullable();
            $table->date('step_12_date')->nullable();
            $table->date('step_13_date')->nullable();
            $table->date('step_14_date')->nullable();
            $table->date('step_15_date')->nullable();

            //Kategori Material atau Jasa, Material dan jasa
            $table->string('kategori')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project');
    }
};
