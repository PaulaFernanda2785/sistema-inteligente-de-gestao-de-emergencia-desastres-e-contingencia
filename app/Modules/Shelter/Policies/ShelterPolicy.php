<?php

namespace App\Modules\Shelter\Policies;

use App\Modules\Admin\Models\User;
use App\Modules\Shelter\Models\Shelter;

class ShelterPolicy
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
        return $user->hasPermission('shelters.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('shelters.create');
    }

    public function update(User $user, Shelter $shelter): bool
    {
        return $user->tenant_id === $shelter->tenant_id
            && $user->hasPermission('shelters.update');
    }

    public function deactivate(User $user, Shelter $shelter): bool
    {
        return $user->tenant_id === $shelter->tenant_id
            && $user->hasPermission('shelters.deactivate');
    }

    private function isActiveUserContext(User $user): bool
    {
        $user->loadMissing('tenant');

        return $user->status === 'ATIVO'
            && $user->tenant !== null
            && (bool) $user->tenant->is_active;
    }
}
