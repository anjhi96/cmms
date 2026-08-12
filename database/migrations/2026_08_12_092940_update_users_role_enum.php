<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE users
            MODIFY role ENUM(
                'ADMIN',
                'KOORDINATOR WWD',
                'KOORDINATOR BUL',
                'PIC WWD',
                'PIC BUL',
                'GUEST'
            ) NOT NULL DEFAULT 'GUEST'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE users
            MODIFY role ENUM(
                'ADMIN',
                'PLANNER',
                'TECHNICIAN',
                'GUEST'
            ) NOT NULL DEFAULT 'GUEST'
        ");
    }
};