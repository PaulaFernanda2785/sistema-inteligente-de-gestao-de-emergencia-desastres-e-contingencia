<?php

namespace App\Modules\Admin\Policies;

use App\Modules\Admin\Models\User;

class UserPolicy
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

    private function isActiveUserContext(User $user): bool
    {
        $user->loadMissing('tenant');

        return $user->status === 'ATIVO'
            && $user->tenant !== null
            && (bool) $user->tenant->is_active;
    }
}
