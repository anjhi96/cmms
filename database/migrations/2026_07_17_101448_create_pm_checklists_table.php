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
        Schema::create('pm_checklists', function (Blueprint $table) {

        $table->id();

        $table->foreignId('pm_schedule_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('machine_checklist_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->enum('clean', [
            'YES',
            'NO'
        ])->nullable();

        $table->enum('lubrication', [
            'YES',
            'NO'
        ])->nullable();

        $table->enum('replace', [
            'YES',
            'NO'
        ])->nullable();

        $table->enum('check', [
            'YES',
            'NO'
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
        Schema::dropIfExists('pm_checklists');
    }
};