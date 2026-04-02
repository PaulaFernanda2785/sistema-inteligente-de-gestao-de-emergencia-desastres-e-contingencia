<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('legal_name', 200);
            $table->string('trade_name', 200)->nullable();
            $table->string('tenant_type', 50);
            $table->string('document_number', 30);
            $table->string('state_code', 2);
            $table->string('city_name', 150)->nullable();
            $table->string('plan_type', 50);
            $table->string('subscription_status', 50);
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('state_code', 'idx_tenants_state_code');
            $table->index('subscription_status', 'idx_tenants_subscription_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
