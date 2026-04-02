<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bairros', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('municipio_id')
                ->constrained('municipios')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('nome', 150);
            $table->string('codigo_ibge', 30)->nullable();
            $table->longText('geojson_referencia')->nullable();
            $table->boolean('ativo')->default(true);

            $table->unique(['municipio_id', 'nome'], 'uq_bairros_municipio_nome');
            $table->unique('codigo_ibge', 'uq_bairros_codigo_ibge');
            $table->index('ativo', 'idx_bairros_ativo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bairros');
    }
};
