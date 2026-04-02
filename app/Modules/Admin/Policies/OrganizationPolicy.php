<?php

namespace App\Modules\Admin\Policies;

use App\Modules\Admin\Models\Organization;
use App\Modules\Admin\Models\User;

class OrganizationPolicy
{
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
}
