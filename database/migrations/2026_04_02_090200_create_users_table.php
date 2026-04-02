<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('organizations')->restrictOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('organizational_units')->nullOnDelete();
            $table->string('name', 200);
            $table->string('email', 150);
            $table->string('cpf_hash', 255)->nullable();
            $table->text('password_hash');
            $table->string('phone', 30)->nullable();
            $table->string('position_name', 150)->nullable();
            $table->string('status', 30)->default('ATIVO');
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'email'], 'uq_users_tenant_email');
            $table->index('tenant_id', 'idx_users_tenant_id');
            $table->index('status', 'idx_users_status');
            $table->index('deleted_at', 'idx_users_deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
