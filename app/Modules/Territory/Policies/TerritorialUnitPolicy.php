<?php

namespace App\Modules\Territory\Policies;

use App\Modules\Admin\Models\User;
use App\Modules\Territory\Models\TerritorialUnit;

class TerritorialUnitPolicy
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
        return $user->hasPermission('territorial_units.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('territorial_units.create');
    }

    public function update(User $user, TerritorialUnit $unit): bool
    {
        return $user->tenant_id === $unit->tenant_id
            && $user->hasPermission('territorial_units.update');
    }

    private function isActiveUserContext(User $user): bool
    {
        $user->loadMissing('tenant');

        return $user->status === 'ATIVO'
            && $user->tenant !== null
            && (bool) $user->tenant->is_active;
    }
}
