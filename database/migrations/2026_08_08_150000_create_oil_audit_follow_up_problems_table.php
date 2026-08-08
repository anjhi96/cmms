<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oil_audit_follow_up_problems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oil_audit_follow_up_id')
                ->constrained('oil_audit_follow_ups')
                ->cascadeOnDelete();
            $table->string('problem');
            $table->timestamps();

            $table->index(['oil_audit_follow_up_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oil_audit_follow_up_problems');
    }
};
