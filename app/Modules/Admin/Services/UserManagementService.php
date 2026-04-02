<?php

namespace App\Modules\Admin\Services;

use App\Core\Services\BaseService;
use App\Modules\Admin\Models\Role;
use App\Modules\Admin\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserManagementService extends BaseService
{
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $tenantId = $this->tenantIdOrFail();

            $user = User::query()->create([
                'tenant_id' => $tenantId,
                'organization_id' => $data['organization_id'],
                'unit_id' => $data['unit_id'] ?? null,
                'name' => $data['name'],
                'email' => $data['email'],
                'cpf_hash' => $data['cpf_hash'] ?? null,
                'password_hash' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'position_name' => $data['position_name'] ?? null,
                'status' => $data['status'],
            ]);

            if (!empty($data['roles'])) {
                $user->roles()->sync($this->filterAllowedRoleIds($tenantId, $data['roles']));
            }

            $this->auditLogger->log(
                module: 'admin.users',
                action: 'create',
                entityType: User::class,
                entityId: $user->id,
                newValues: $this->sanitizeAuditValues($user->fresh()->toArray()),
            );

            return $user->fresh(['organization:id,name', 'roles:id,name']);
        });
    }

    public function update(int $userId, array $data): User
    {
        return DB::transaction(function () use ($userId, $data): User {
            $tenantId = $this->tenantIdOrFail();

            $user = User::query()
                ->where('tenant_id', $tenantId)
                ->findOrFail($userId);

            $oldValues = $this->sanitizeAuditValues($user->toArray());

            $payload = [
                'organization_id' => $data['organization_id'],
                'unit_id' => $data['unit_id'] ?? null,
                'name' => $data['name'],
                'email' => $data['email'],
                'cpf_hash' => $data['cpf_hash'] ?? null,
                'phone' => $data['phone'] ?? null,
                'position_name' => $data['position_name'] ?? null,
                'status' => $data['status'],
            ];

            if (!empty($data['password'])) {
                $payload['password_hash'] = Hash::make($data['password']);
            }

            $user->update($payload);

            if (array_key_exists('roles', $data)) {
                $user->roles()->sync($this->filterAllowedRoleIds($tenantId, $data['roles'] ?? []));
            }

            $this->auditLogger->log(
                module: 'admin.users',
                action: 'update',
                entityType: User::class,
                entityId: $user->id,
                oldValues: $oldValues,
                newValues: $this->sanitizeAuditValues($user->fresh()->toArray()),
            );

            return $user->fresh(['organization:id,name', 'roles:id,name']);
        });
    }

    public function deactivate(int $userId, int $actingUserId): User
    {
        return DB::transaction(function () use ($userId, $actingUserId): User {
            $tenantId = $this->tenantIdOrFail();

            if ($userId === $actingUserId) {
                throw new AuthorizationException('Não é permitido inativar o próprio usuário.');
            }

            $user = User::query()
                ->where('tenant_id', $tenantId)
                ->findOrFail($userId);

            $oldValues = $this->sanitizeAuditValues($user->toArray());

            $user->update([
                'status' => 'INATIVO',
                'deleted_at' => now(),
            ]);

            $this->auditLogger->log(
                module: 'admin.users',
                action: 'deactivate',
                entityType: User::class,
                entityId: $user->id,
                oldValues: $oldValues,
                newValues: $this->sanitizeAuditValues($user->fresh()->toArray()),
            );

            return $user->fresh(['organization:id,name', 'roles:id,name']);
        });
    }

    /**
     * @param array<int, int|string> $roleIds
     * @return array<int, int>
     */
    private function filterAllowedRoleIds(int $tenantId, array $roleIds): array
    {
        $normalizedRoleIds = array_map(static fn ($roleId): int => (int) $roleId, $roleIds);

        return Role::query()
            ->whereIn('id', $normalizedRoleIds)
            ->where(function ($query) use ($tenantId): void {
                $query->where('tenant_id', $tenantId)->orWhereNull('tenant_id');
            })
            ->pluck('id')
            ->all();
    }

    private function sanitizeAuditValues(array $values): array
    {
        unset($values['password_hash'], $values['remember_token']);

        return $values;
    }
}
