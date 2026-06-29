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
        Schema::create('spareparts', function (Blueprint $table) {

            $table->id();

            $table->string('material_number')->unique();
            $table->string('location')->nullable();

            $table->string('description');

            $table->text('remarks')->nullable();

            $table->decimal('stock', 12, 2)->default(0);

            $table->string('unit')->nullable();

            $table->integer('rop')->default(0);

            $table->string('mrp_type')->nullable();

            $table->decimal('price', 15, 2)->default(0);

            $table->enum('status', [
                'ACTIVE',
                'INACTIVE'
            ])->default('ACTIVE');

            $table->string('machine_type')->nullable();

            $table->string('segment')->nullable();

            $table->string('pdt')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spareparts');
    }
};
