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
        Schema::table('machine_checklists', function (Blueprint $table) {

            $table->enum('maintenance_type', [
                'check',
                'clean',
                'lubrication',
                'replace',
            ])
            ->default('check')
            ->after('checklist_item');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machine_checklists', function (Blueprint $table) {
            
            $table->dropColumn('maintenance_type');

        });
    }
};
