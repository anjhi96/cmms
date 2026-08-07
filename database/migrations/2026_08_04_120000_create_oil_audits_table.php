<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oil_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained()->cascadeOnDelete();

            // Snapshot agar laporan tetap akurat jika master mesin berubah.
            $table->string('machine_number');
            $table->string('machine_type');
            $table->string('area');

            $table->enum('condition', [
                'OKE',
                'PANTAU',
                'HAMPIR_GARIS',
                'PAS_GARIS',
                'KRITIS',
                'OLI_KERUH',
                'GLASS_BUREM',
            ]);

            $table->foreignId('audited_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('audited_by_name');
            $table->timestamp('audited_at');
            $table->timestamps();

            $table->index(['machine_id', 'audited_at']);
            $table->index(['condition', 'audited_at']);
            $table->index('machine_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oil_audits');
    }
};
