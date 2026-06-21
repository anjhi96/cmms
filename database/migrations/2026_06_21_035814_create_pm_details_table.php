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
        Schema::create('pm_details', function (Blueprint $table) {

        $table->id();

        // RELATION KE PM SCHEDULE
        $table->foreignId('pm_schedule_id')
            ->constrained('pm_schedules')
            ->onDelete('cascade');

        // ITEM MEASUREMENT
        $table->string('measurement_item');

        $table->string('measurement_value')->nullable();

        $table->string('unit')->nullable(); // °C, mm, bar, dll

        $table->text('remarks')->nullable();

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pm_details');
    }
};
