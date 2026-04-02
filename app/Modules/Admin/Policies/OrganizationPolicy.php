<?php

namespace App\Modules\Admin\Policies;

use App\Modules\Admin\Models\Organization;
use App\Modules\Admin\Models\User;

class OrganizationPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if (!$this->isActiveUserContext($user)) {
            return false;
        }

        return null;
    }

    public function view(User $user, Organization $organization): bool
    {
        return $user->tenant_id === $organization->tenant_id
            && $user->hasPermission('organizations.view');
    }

    public function update(User $user, Organization $organization): bool
    {
        return $user->tenant_id === $organization->tenant_id
            && $user->hasPermission('organizations.update');
    }

    private function isActiveUserContext(User $user): bool
    {
        $user->loadMissing('tenant');

        return $user->status === 'ATIVO'
            && $user->tenant !== null
            && (bool) $user->tenant->is_active;
    }
}
