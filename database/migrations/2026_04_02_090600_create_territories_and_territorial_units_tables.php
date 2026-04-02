<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('territories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name', 200);
            $table->string('territory_type', 100);
            $table->string('ibge_code', 20)->nullable();
            $table->string('state_code', 2);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('tenant_id', 'idx_territories_tenant_id');
            $table->index('ibge_code', 'idx_territories_ibge_code');
        });

        Schema::create('territorial_units', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('territory_id')->constrained('territories')->cascadeOnDelete();
            $table->foreignId('parent_unit_id')->nullable()->constrained('territorial_units')->nullOnDelete();
            $table->string('name', 200);
            $table->string('unit_type', 100);
            $table->string('code', 50)->nullable();
            $table->unsignedInteger('population_estimate')->nullable();
            $table->timestamps();

            $table->index('tenant_id', 'idx_territorial_units_tenant_id');
            $table->index('territory_id', 'idx_territorial_units_territory_id');
            $table->index('unit_type', 'idx_territorial_units_unit_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('territorial_units');
        Schema::dropIfExists('territories');
    }
};
