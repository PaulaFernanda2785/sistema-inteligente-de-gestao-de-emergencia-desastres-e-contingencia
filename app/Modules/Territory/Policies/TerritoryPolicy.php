<?php

namespace App\Modules\Territory\Policies;

use App\Modules\Admin\Models\User;
use App\Modules\Territory\Models\Territory;

class TerritoryPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if (!$this->isActiveUserContext($user)) {
            return false;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('territories.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('territories.create');
    }

    public function update(User $user, Territory $territory): bool
    {
        return $user->tenant_id === $territory->tenant_id
            && $user->hasPermission('territories.update');
    }

    private function isActiveUserContext(User $user): bool
    {
        $user->loadMissing('tenant');

        return $user->status === 'ATIVO'
            && $user->tenant !== null
            && (bool) $user->tenant->is_active;
    }
}
