<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shelters', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('territorial_unit_id')->constrained('territorial_units')->restrictOnDelete();
            $table->string('name', 200);
            $table->string('shelter_type', 100);
            $table->text('address');
            $table->string('manager_name', 150)->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->unsignedInteger('max_people_capacity');
            $table->text('accessibility_features')->nullable();
            $table->boolean('kitchen_available')->default(false);
            $table->boolean('water_supply_available')->default(false);
            $table->boolean('energy_supply_available')->default(false);
            $table->text('sanitary_structure_description')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('tenant_id', 'idx_shelters_tenant_id');
            $table->index('territorial_unit_id', 'idx_shelters_territorial_unit_id');
            $table->index('is_active', 'idx_shelters_is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shelters');
    }
};
