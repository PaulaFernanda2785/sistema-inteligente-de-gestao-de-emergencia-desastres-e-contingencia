<?php

namespace App\Modules\Admin\Policies;

use App\Modules\Admin\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('users.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('users.create');
    }

    public function update(User $user, User $target): bool
    {
        return $user->tenant_id === $target->tenant_id
            && $user->hasPermission('users.update');
    }

    public function deactivate(User $user, User $target): bool
    {
        return $user->tenant_id === $target->tenant_id
            && $user->hasPermission('users.deactivate');
    }
}
