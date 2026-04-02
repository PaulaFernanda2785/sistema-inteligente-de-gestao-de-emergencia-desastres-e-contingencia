<?php

namespace App\Modules\Risk\Policies;

use App\Modules\Admin\Models\User;
use App\Modules\Risk\Models\RiskArea;

class RiskAreaPolicy
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
        return $user->hasPermission('risk_areas.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('risk_areas.create');
    }

    public function update(User $user, RiskArea $riskArea): bool
    {
        return $user->tenant_id === $riskArea->tenant_id
            && $user->hasPermission('risk_areas.update');
    }

    public function deactivate(User $user, RiskArea $riskArea): bool
    {
        return $user->tenant_id === $riskArea->tenant_id
            && $user->hasPermission('risk_areas.deactivate');
    }

    private function isActiveUserContext(User $user): bool
    {
        $user->loadMissing('tenant');

        return $user->status === 'ATIVO'
            && $user->tenant !== null
            && (bool) $user->tenant->is_active;
    }
}
