<?php

namespace App\Modules\Admin\Repositories;

use App\Modules\Admin\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository
{
    public function paginateByFilters(int $tenantId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->with(['organization:id,name', 'roles:id,name'])
            ->where('tenant_id', $tenantId)
            ->when(
                $filters['name'] ?? null,
                fn ($query, string $value) => $query->where('name', 'like', "%{$value}%"),
            )
            ->when(
                $filters['email'] ?? null,
                fn ($query, string $value) => $query->where('email', 'like', "%{$value}%"),
            )
            ->when(
                $filters['status'] ?? null,
                fn ($query, string $value) => $query->where('status', $value),
            )
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }
}
