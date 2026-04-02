<?php

namespace Database\Seeders;

use App\Modules\Admin\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['code' => 'users.view', 'name' => 'Visualizar usuários', 'module' => 'admin.users', 'action' => 'view'],
            ['code' => 'users.create', 'name' => 'Criar usuários', 'module' => 'admin.users', 'action' => 'create'],
            ['code' => 'users.update', 'name' => 'Atualizar usuários', 'module' => 'admin.users', 'action' => 'update'],
            ['code' => 'users.deactivate', 'name' => 'Inativar usuários', 'module' => 'admin.users', 'action' => 'deactivate'],
            ['code' => 'organizations.view', 'name' => 'Visualizar organização', 'module' => 'admin.organizations', 'action' => 'view'],
            ['code' => 'organizations.update', 'name' => 'Atualizar organização', 'module' => 'admin.organizations', 'action' => 'update'],
            ['code' => 'audit.view', 'name' => 'Visualizar auditoria', 'module' => 'audit', 'action' => 'view'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(
                ['code' => $permission['code']],
                $permission,
            );
        }
    }
}
