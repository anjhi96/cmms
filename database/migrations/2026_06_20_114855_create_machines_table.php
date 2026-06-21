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
        Schema::create('machines', function (Blueprint $table) {
            $table->id();

            $table->string('area');               // KA-WWD / KA-C&B
            $table->string('machine_type');        // NDE, NDB, dll
            $table->string('machine_number')->unique();

            $table->string('description')->nullable();
            $table->string('status')->default('ACTIVE');

            $table->date('install_date')->nullable();
            $table->string('criticality')->nullable(); // A / B / C

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index(['area', 'machine_type', 'status']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
