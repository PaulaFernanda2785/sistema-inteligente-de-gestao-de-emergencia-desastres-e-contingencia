<?php

namespace App\Modules\Territory\Services;

use App\Core\Services\BaseService;
use App\Modules\Territory\Models\Bairro;
use App\Modules\Territory\Models\Municipio;
use Illuminate\Support\Facades\DB;

class BairroService extends BaseService
{
    public function create(array $data): Bairro
    {
        return DB::transaction(function () use ($data): Bairro {
            $municipio = $this->resolveMunicipio((int) $data['municipio_id']);

            $bairro = Bairro::query()->create([
                'municipio_id' => $municipio->id,
                'nome' => trim($data['nome']),
                'codigo_ibge' => $data['codigo_ibge'] ?? null,
                'geojson_referencia' => $data['geojson_referencia'] ?? null,
                'ativo' => array_key_exists('ativo', $data) ? (bool) $data['ativo'] : true,
            ]);

            $this->auditLogger->log(
                module: 'territory.bairros',
                action: 'create',
                entityType: Bairro::class,
                entityId: $bairro->id,
                newValues: $bairro->toArray(),
            );

            return $bairro->fresh(['municipio:id,nome,uf']);
        });
    }

    public function update(Bairro $bairro, array $data): Bairro
    {
        return DB::transaction(function () use ($bairro, $data): Bairro {
            $municipio = $this->resolveMunicipio((int) $data['municipio_id']);
            $oldValues = $bairro->toArray();

            $bairro->update([
                'municipio_id' => $municipio->id,
                'nome' => trim($data['nome']),
                'codigo_ibge' => $data['codigo_ibge'] ?? null,
                'geojson_referencia' => $data['geojson_referencia'] ?? null,
                'ativo' => array_key_exists('ativo', $data) ? (bool) $data['ativo'] : $bairro->ativo,
            ]);

            $this->auditLogger->log(
                module: 'territory.bairros',
                action: 'update',
                entityType: Bairro::class,
                entityId: $bairro->id,
                oldValues: $oldValues,
                newValues: $bairro->fresh()->toArray(),
            );

            return $bairro->fresh(['municipio:id,nome,uf']);
        });
    }

    private function resolveMunicipio(int $municipioId): Municipio
    {
        return Municipio::query()
            ->where('ativo', true)
            ->findOrFail($municipioId);
    }
}
