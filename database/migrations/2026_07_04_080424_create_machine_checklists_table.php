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
        Schema::create('machine_checklists', function (Blueprint $table) {

            $table->id();

            // Machine Type
            $table->string('machine_type');

            // Section (Cone 1, Cone 2, Machine Item, dll)
            $table->string('section');

            // Urutan Section
            $table->unsignedInteger('section_order')->default(1);

            // Checklist Item
            $table->string('checklist_item');

            $table->enum('maintenance_type', [
                'check',
                'clean',
                'lubrication',
                'replace',
            ])
            ->default('check');

            // Urutan Item dalam Section
            $table->unsignedInteger('item_order')->default(1);

            $table->timestamps();

            // Hindari duplicate checklist
            $table->unique([
                'machine_type',
                'section',
                'checklist_item'
            ], 'machine_checklist_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_checklists');
    }
};
