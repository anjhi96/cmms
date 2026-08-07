<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class () extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Backfill existing pm_schedules which have actual_date into pm_work_sessions as Day 1
        // Only insert if no work sessions exist for that pm_schedule
        DB::table('pm_schedules')
            ->select('id', 'actual_date', 'start_time', 'end_time', 'duration')
            ->whereNotNull('actual_date')
            ->orderBy('id')
            ->chunk(100, function ($schedules) {
                foreach ($schedules as $s) {
                    $exists = DB::table('pm_work_sessions')
                        ->where('pm_schedule_id', $s->id)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    DB::table('pm_work_sessions')->insert([
                        'pm_schedule_id' => $s->id,
                        'actual_date' => $s->actual_date,
                        'start_time' => $s->start_time,
                        'end_time' => $s->end_time,
                        'duration' => $s->duration,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No automatic rollback for data backfill
    }
};
