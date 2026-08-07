<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pm_work_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pm_schedule_id')
                ->constrained('pm_schedules')
                ->onDelete('cascade');

            $table->date('actual_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            // stored in minutes
            $table->integer('duration')->nullable();

            $table->timestamps();

            $table->index('pm_schedule_id');
            $table->index('actual_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pm_work_sessions');
    }
};
