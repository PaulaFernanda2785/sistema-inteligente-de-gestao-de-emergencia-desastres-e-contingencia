<?php

namespace App\Modules\Territory\Repositories;

use App\Modules\Territory\Models\TerritorialUnit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TerritorialUnitRepository
{
    public function paginateByFilters(int $tenantId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return TerritorialUnit::query()
            ->with(['territory:id,name', 'parent:id,name'])
            ->where('tenant_id', $tenantId)
            ->when(
                $filters['territory_id'] ?? null,
                fn ($query, int|string $value) => $query->where('territory_id', (int) $value),
            )
            ->when(
                $filters['name'] ?? null,
                fn ($query, string $value) => $query->where('name', 'like', "%{$value}%"),
            )
            ->when(
                $filters['unit_type'] ?? null,
                fn ($query, string $value) => $query->where('unit_type', $value),
            )
            ->orderBy('territory_id')
            ->orderBy('parent_unit_id')
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }
}
