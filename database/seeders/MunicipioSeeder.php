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

        $rows = [];
        foreach ($payload as $item) {
            if (!is_array($item)) {
                continue;
            }

            $rows[] = [
                'nome' => (string) ($item['nome'] ?? ''),
                'codigo_ibge' => (string) ($item['codigo_ibge'] ?? ''),
                'uf' => strtoupper((string) ($item['uf'] ?? '')),
                'ativo' => (bool) ($item['ativo'] ?? true),
            ];
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('municipios')->upsert(
                $chunk,
                ['codigo_ibge'],
                ['nome', 'uf', 'ativo'],
            );
        }
    }
}
