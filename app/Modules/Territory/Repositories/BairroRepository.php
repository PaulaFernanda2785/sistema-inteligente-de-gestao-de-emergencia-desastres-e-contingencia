<?php

namespace App\Modules\Territory\Repositories;

use App\Modules\Territory\Models\Bairro;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BairroRepository
{
    public function paginateByFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Bairro::query()
            ->with(['municipio:id,nome,uf'])
            ->when(
                $filters['municipio_id'] ?? null,
                fn ($query, int|string $value) => $query->where('municipio_id', (int) $value),
            )
            ->when(
                $filters['nome'] ?? null,
                fn ($query, string $value) => $query->where('nome', 'like', "%{$value}%"),
            )
            ->when(
                $filters['codigo_ibge'] ?? null,
                fn ($query, string $value) => $query->where('codigo_ibge', $value),
            )
            ->when(
                array_key_exists('ativo', $filters) && $filters['ativo'] !== '' ? (string) $filters['ativo'] : null,
                fn ($query, string $value) => $query->where('ativo', $value === '1'),
            )
            ->orderBy('municipio_id')
            ->orderBy('nome')
            ->paginate($perPage)
            ->withQueryString();
    }
}
