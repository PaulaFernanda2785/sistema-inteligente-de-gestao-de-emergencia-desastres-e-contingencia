<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estados', function (Blueprint $table): void {
            $table->id();
            $table->string('nome', 100);
            $table->char('sigla', 2);
            $table->string('codigo_ibge', 2);
            $table->string('regiao', 30);
            $table->boolean('ativo')->default(true);

            $table->unique('nome', 'uq_estados_nome');
            $table->unique('sigla', 'uq_estados_sigla');
            $table->unique('codigo_ibge', 'uq_estados_codigo_ibge');
            $table->index('ativo', 'idx_estados_ativo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estados');
    }
};
