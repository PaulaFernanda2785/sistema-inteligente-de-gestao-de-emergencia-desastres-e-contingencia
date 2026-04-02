<?php

namespace App\Modules\Risk\Repositories;

use App\Modules\Risk\Models\RiskArea;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RiskAreaRepository
{
    public function paginateByFilters(int $tenantId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return RiskArea::query()
            ->with(['territorialUnit:id,name,territory_id'])
            ->where('tenant_id', $tenantId)
            ->when(
                $filters['territorial_unit_id'] ?? null,
                fn ($query, int|string $value) => $query->where('territorial_unit_id', (int) $value),
            )
            ->when(
                $filters['risk_type'] ?? null,
                fn ($query, string $value) => $query->where('risk_type', $value),
            )
            ->when(
                $filters['priority_level'] ?? null,
                fn ($query, string $value) => $query->where('priority_level', $value),
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
