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
        Schema::create('pm_spareparts', function (Blueprint $table) {

            $table->id();

            $table->foreignId('pm_schedule_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('sparepart_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('qty');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pm_spareparts');
    }
};
