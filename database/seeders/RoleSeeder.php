<?php

namespace Database\Seeders;

use App\Modules\Admin\Models\Permission;
use App\Modules\Admin\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $tenantAdminRole = Role::query()->firstOrCreate(
            ['tenant_id' => null, 'code' => 'TENANT_ADMIN'],
            [
                'name' => 'Administrador do Tenant',
                'description' => 'Perfil administrativo padrao do tenant.',
                'is_system_role' => true,
            ],
        );

        $auditorRole = Role::query()->firstOrCreate(
            ['tenant_id' => null, 'code' => 'AUDITOR'],
            [
                'name' => 'Auditor',
                'description' => 'Perfil de auditoria e consulta.',
                'is_system_role' => true,
            ],
        );

        $tenantAdminPermissionCodes = [
            'users.view',
            'users.create',
            'users.update',
            'users.deactivate',
            'organizations.view',
            'organizations.update',
            'territories.view',
            'territories.create',
            'territories.update',
            'territorial_units.view',
            'territorial_units.create',
            'territorial_units.update',
            'audit.view',
        ];

        $auditorPermissionCodes = [
            'audit.view',
            'users.view',
            'organizations.view',
            'territories.view',
            'territorial_units.view',
        ];

        $tenantAdminRole->permissions()->sync(
            Permission::query()->whereIn('code', $tenantAdminPermissionCodes)->pluck('id')->all(),
        );

        $auditorRole->permissions()->sync(
            Permission::query()->whereIn('code', $auditorPermissionCodes)->pluck('id')->all(),
        );
    }
}
