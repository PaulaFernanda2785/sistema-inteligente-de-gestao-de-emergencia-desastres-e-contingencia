<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name', 200);
            $table->string('acronym', 50)->nullable();
            $table->string('organization_type', 100);
            $table->text('address')->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('coordinator_name', 150)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('tenant_id', 'idx_organizations_tenant_id');
            $table->index('is_active', 'idx_organizations_is_active');
        });

        Schema::create('organizational_units', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('parent_unit_id')->nullable()->constrained('organizational_units')->nullOnDelete();
            $table->string('name', 200);
            $table->string('unit_type', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('tenant_id', 'idx_organizational_units_tenant_id');
            $table->index('organization_id', 'idx_organizational_units_organization_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizational_units');
        Schema::dropIfExists('organizations');
    }
};
