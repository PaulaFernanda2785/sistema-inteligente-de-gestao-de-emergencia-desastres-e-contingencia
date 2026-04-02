<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('territorial_units', function (Blueprint $table): void {
            $table->foreignId('municipio_id')
                ->nullable()
                ->after('territory_id')
                ->constrained('municipios')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('bairro_id')
                ->nullable()
                ->after('municipio_id')
                ->constrained('bairros')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->index('municipio_id', 'idx_territorial_units_municipio_id');
            $table->index('bairro_id', 'idx_territorial_units_bairro_id');
        });
    }

    public function down(): void
    {
        Schema::table('territorial_units', function (Blueprint $table): void {
            $table->dropIndex('idx_territorial_units_municipio_id');
            $table->dropIndex('idx_territorial_units_bairro_id');
            $table->dropConstrainedForeignId('bairro_id');
            $table->dropConstrainedForeignId('municipio_id');
        });
    }
};
