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
        Schema::create('plan_step_duration', function (Blueprint $table) {
            $table->id('id_plan_step_duration');
            $table->integer('id_project');
            $table->integer('notif');
            $table->integer('rekomend');
            $table->integer('job_plan');
            $table->integer('wo');
            $table->integer('mat_reser');
            $table->integer('pr');
            $table->integer('tender');
            $table->integer('po');
            $table->integer('gr');
            $table->integer('gi');
            $table->integer('eksekusi');
            $table->integer('test_perfo');
            $table->integer('sa');
            $table->integer('update_or');
            $table->integer('closed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_step_duration');
    }
}; 