<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('municipios', function (Blueprint $table): void {
            $table->foreignId('estado_id')
                ->nullable()
                ->after('id')
                ->constrained('estados')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->index('estado_id', 'idx_municipios_estado_id');
        });
    }

    public function down(): void
    {
        Schema::table('municipios', function (Blueprint $table): void {
            $table->dropIndex('idx_municipios_estado_id');
            $table->dropConstrainedForeignId('estado_id');
        });
    }
};
