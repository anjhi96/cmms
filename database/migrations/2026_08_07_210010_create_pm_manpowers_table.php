<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('pm_manpowers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pm_work_session_id')
                ->constrained('pm_work_sessions')
                ->onDelete('cascade');
            $table->string('person');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('man_hour')->nullable();
            $table->timestamps();

            $table->index('pm_work_session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_manpowers');
    }
};
