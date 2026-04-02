<?php

namespace App\Modules\Shelter\Repositories;

use App\Modules\Shelter\Models\Shelter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ShelterRepository
{
    public function paginateByFilters(int $tenantId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Shelter::query()
            ->with(['territorialUnit:id,name'])
            ->where('tenant_id', $tenantId)
            ->when(
                $filters['territorial_unit_id'] ?? null,
                fn ($query, int|string $value) => $query->where('territorial_unit_id', (int) $value),
            )
            ->when(
                $filters['shelter_type'] ?? null,
                fn ($query, string $value) => $query->where('shelter_type', $value),
            )
            ->when(
                array_key_exists('is_active', $filters) && $filters['is_active'] !== '' ? (string) $filters['is_active'] : null,
                fn ($query, string $value) => $query->where('is_active', $value === '1'),
            )
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }
}
