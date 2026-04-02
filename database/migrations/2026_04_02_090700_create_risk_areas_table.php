<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_areas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('territorial_unit_id')->constrained('territorial_units')->cascadeOnDelete();
            $table->string('name', 200);
            $table->string('risk_type', 100);
            $table->string('priority_level', 30);
            $table->unsignedInteger('exposed_population_estimate')->nullable();
            $table->text('description')->nullable();
            $table->text('monitoring_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('tenant_id', 'idx_risk_areas_tenant_id');
            $table->index('territorial_unit_id', 'idx_risk_areas_territorial_unit_id');
            $table->index('risk_type', 'idx_risk_areas_risk_type');
            $table->index('priority_level', 'idx_risk_areas_priority_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_areas');
    }
};
