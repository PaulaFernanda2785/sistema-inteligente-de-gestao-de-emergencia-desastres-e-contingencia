<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MunicipioSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/municipios_ibge.json');
        if (!is_file($path)) {
            throw new RuntimeException('Arquivo de municipios nao encontrado em database/data/municipios_ibge.json.');
        }

        $payload = json_decode((string) file_get_contents($path), true);
        if (!is_array($payload)) {
            throw new RuntimeException('Arquivo de municipios invalido.');
        }

        $estadoBySigla = DB::table('estados')
            ->pluck('id', 'sigla')
            ->map(fn (mixed $value): int => (int) $value)
            ->all();
        $estadoByCodigo = DB::table('estados')
            ->pluck('id', 'codigo_ibge')
            ->map(fn (mixed $value): int => (int) $value)
            ->all();
        $siglaByEstadoId = DB::table('estados')
            ->pluck('sigla', 'id')
            ->all();

        $rows = [];
        foreach ($payload as $item) {
            if (!is_array($item)) {
                continue;
            }

            $uf = strtoupper((string) ($item['uf'] ?? ''));
            $codigoMunicipio = (string) ($item['codigo_ibge'] ?? '');
            $codigoEstado = str_pad(substr($codigoMunicipio, 0, 2), 2, '0', STR_PAD_LEFT);

            $estadoId = $estadoBySigla[$uf] ?? ($estadoByCodigo[$codigoEstado] ?? null);
            if ($estadoId === null) {
                throw new RuntimeException("Estado nao encontrado para municipio [{$codigoMunicipio}] com sigla [{$uf}].");
            }

            if ($uf === '') {
                $uf = (string) ($siglaByEstadoId[$estadoId] ?? '');
            }

            $rows[] = [
                'estado_id' => $estadoId,
                'nome' => (string) ($item['nome'] ?? ''),
                'codigo_ibge' => $codigoMunicipio,
                'uf' => $uf,
                'ativo' => (bool) ($item['ativo'] ?? true),
            ];
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('municipios')->upsert(
                $chunk,
                ['codigo_ibge'],
                ['estado_id', 'nome', 'uf', 'ativo'],
            );
        }
    }
}
