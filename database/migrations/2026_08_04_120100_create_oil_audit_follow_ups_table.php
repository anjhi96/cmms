<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oil_audit_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oil_audit_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('problem');
            $table->text('action_taken');
            $table->foreignId('pic_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('pic_name');
            $table->timestamp('actioned_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oil_audit_follow_ups');
    }
};
