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
        Schema::create('pm_schedules', function (Blueprint $table) {

            $table->id();

            // Relation
            $table->foreignId('machine_id')
                ->constrained()
                ->onDelete('cascade');

            // Snapshot Asset (WAJIB)
            $table->string('area');
            $table->string('machine_type');
            $table->string('machine_number');

            // Order
            $table->string('order_number')->unique()->nullable();

            // Planning
            $table->date('plan_date');
            $table->string('plan_month');
            $table->year('plan_year');
            $table->date('due_date')->nullable();
            $table->date('last_pm')->nullable();

            // Assignment
            $table->string('pic')->nullable();
            $table->string('oil_change')->nullable();
            $table->string('greasing')->nullable();
            $table->string('wo_zsbp')->nullable();

            // Execution
            $table->date('actual_date')->nullable();

            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            // disimpan dalam menit
            $table->integer('duration')->nullable();

            $table->text('remarks')->nullable();

            // Next PM
            $table->date('next_pm')->nullable();

            // Status
            $table->enum('status', [
                'OPEN',
                'IN_PROGRESS',
                'FINISHED',
                'FINISHED_ON_TIME',
                'MISSED',
            ])->default('OPEN');

            $table->timestamps();

            // Index
            $table->index('machine_number');
            $table->index('machine_type');
            $table->index('plan_month');
            $table->index('plan_year');
            $table->index('status');
            $table->index('area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pm_schedules');
    }
};
