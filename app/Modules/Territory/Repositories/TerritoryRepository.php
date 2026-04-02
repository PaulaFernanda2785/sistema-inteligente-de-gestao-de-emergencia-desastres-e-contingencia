<?php

namespace App\Modules\Territory\Repositories;

use App\Modules\Territory\Models\Territory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TerritoryRepository
{
    public function paginateByFilters(int $tenantId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Territory::query()
            ->where('tenant_id', $tenantId)
            ->withCount('units')
            ->when(
                $filters['name'] ?? null,
                fn ($query, string $value) => $query->where('name', 'like', "%{$value}%"),
            )
            ->when(
                $filters['territory_type'] ?? null,
                fn ($query, string $value) => $query->where('territory_type', $value),
            )
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }
}
