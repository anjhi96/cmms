<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('pm_work_sessions')) {
            return;
        }

        $schedules = DB::table('pm_schedules')
            ->whereNotNull('actual_date')
            ->whereNotNull('start_time')
            ->whereNotNull('end_time')
            ->whereNotNull('duration')
            ->get();

        foreach ($schedules as $schedule) {
            $exists = DB::table('pm_work_sessions')
                ->where('pm_schedule_id', $schedule->id)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('pm_work_sessions')->insert([
                'pm_schedule_id' => $schedule->id,
                'actual_date' => $schedule->actual_date,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'duration' => $schedule->duration,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // no-op: data backfill should remain if rolled back
    }
};
