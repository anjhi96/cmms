<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pm_measurements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pm_schedule_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('machine_measurement_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('measurement_item');
            $table->string('standard')->nullable();
            $table->string('measurement_value')->nullable();
            $table->string('unit')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pm_measurements');
    }
};
