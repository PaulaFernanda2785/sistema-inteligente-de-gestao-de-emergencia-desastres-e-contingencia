<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('municipios', function (Blueprint $table): void {
            $table->id();
            $table->string('nome', 150);
            $table->string('codigo_ibge', 20)->nullable();
            $table->char('uf', 2)->default('PA');
            $table->boolean('ativo')->default(true);

            $table->unique(['nome', 'uf'], 'uq_municipios_nome_uf');
            $table->unique('codigo_ibge', 'uq_municipios_codigo_ibge');
            $table->index('ativo', 'idx_municipios_ativo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('municipios');
    }
};
