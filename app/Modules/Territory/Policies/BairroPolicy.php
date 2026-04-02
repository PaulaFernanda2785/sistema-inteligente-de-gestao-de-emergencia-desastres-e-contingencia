<?php

namespace App\Modules\Territory\Policies;

use App\Modules\Admin\Models\User;
use App\Modules\Territory\Models\Bairro;

class BairroPolicy
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
        return $user->hasPermission('bairros.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('bairros.create');
    }

    public function update(User $user, Bairro $bairro): bool
    {
        return $user->hasPermission('bairros.update');
    }

    private function isActiveUserContext(User $user): bool
    {
        $user->loadMissing('tenant');

        return $user->status === 'ATIVO'
            && $user->tenant !== null
            && (bool) $user->tenant->is_active;
    }
}
