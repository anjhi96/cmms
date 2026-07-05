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
        Schema::create('pm_problems', function (Blueprint $table) {

            $table->id();

            $table->foreignId('pm_schedule_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('machine_problem_id')
                ->constrained('machine_problems')
                ->cascadeOnDelete();

            $table->foreignId('machine_problem_finding_id')
                ->nullable()
                ->constrained('machine_problem_findings')
                ->nullOnDelete();

            $table->enum('severity', [
                'Low',
                'Medium',
                'High',
            ])->nullable();

            $table->enum('oil_change', [
                'YES',
                'NO',
            ])->nullable();

            $table->enum('greasing', [
                'YES',
                'NO',
            ])->nullable();

            $table->enum('wo_zsbp', [
                'YES',
                'NO',
            ])->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pm_problems');
    }
};
