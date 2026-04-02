<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('active_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('session_token_hash');
            $table->string('ip_address', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('last_activity_at');
            $table->timestamp('expires_at');
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id', 'idx_active_sessions_user_id');
            $table->index('expires_at', 'idx_active_sessions_expires_at');
        });

        Schema::create('security_policies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->unique()->constrained('tenants')->cascadeOnDelete();
            $table->json('password_policy_json')->nullable();
            $table->unsignedInteger('session_timeout_minutes');
            $table->boolean('mfa_required')->default(false);
            $table->boolean('allow_external_users')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_policies');
        Schema::dropIfExists('active_sessions');
    }
};
