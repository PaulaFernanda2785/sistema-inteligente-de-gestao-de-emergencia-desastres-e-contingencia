<?php

namespace Database\Seeders;

use App\Modules\Admin\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['code' => 'users.view', 'name' => 'Visualizar usuarios', 'module' => 'admin.users', 'action' => 'view'],
            ['code' => 'users.create', 'name' => 'Criar usuarios', 'module' => 'admin.users', 'action' => 'create'],
            ['code' => 'users.update', 'name' => 'Atualizar usuarios', 'module' => 'admin.users', 'action' => 'update'],
            ['code' => 'users.deactivate', 'name' => 'Inativar usuarios', 'module' => 'admin.users', 'action' => 'deactivate'],
            ['code' => 'organizations.view', 'name' => 'Visualizar organizacao', 'module' => 'admin.organizations', 'action' => 'view'],
            ['code' => 'organizations.update', 'name' => 'Atualizar organizacao', 'module' => 'admin.organizations', 'action' => 'update'],
            ['code' => 'territories.view', 'name' => 'Visualizar territorios', 'module' => 'territory.territories', 'action' => 'view'],
            ['code' => 'territories.create', 'name' => 'Criar territorios', 'module' => 'territory.territories', 'action' => 'create'],
            ['code' => 'territories.update', 'name' => 'Atualizar territorios', 'module' => 'territory.territories', 'action' => 'update'],
            ['code' => 'territorial_units.view', 'name' => 'Visualizar unidades territoriais', 'module' => 'territory.units', 'action' => 'view'],
            ['code' => 'territorial_units.create', 'name' => 'Criar unidades territoriais', 'module' => 'territory.units', 'action' => 'create'],
            ['code' => 'territorial_units.update', 'name' => 'Atualizar unidades territoriais', 'module' => 'territory.units', 'action' => 'update'],
            ['code' => 'risk_areas.view', 'name' => 'Visualizar areas de risco', 'module' => 'risk.areas', 'action' => 'view'],
            ['code' => 'risk_areas.create', 'name' => 'Criar areas de risco', 'module' => 'risk.areas', 'action' => 'create'],
            ['code' => 'risk_areas.update', 'name' => 'Atualizar areas de risco', 'module' => 'risk.areas', 'action' => 'update'],
            ['code' => 'risk_areas.deactivate', 'name' => 'Inativar areas de risco', 'module' => 'risk.areas', 'action' => 'deactivate'],
            ['code' => 'shelters.view', 'name' => 'Visualizar abrigos', 'module' => 'shelter', 'action' => 'view'],
            ['code' => 'shelters.create', 'name' => 'Criar abrigos', 'module' => 'shelter', 'action' => 'create'],
            ['code' => 'shelters.update', 'name' => 'Atualizar abrigos', 'module' => 'shelter', 'action' => 'update'],
            ['code' => 'shelters.deactivate', 'name' => 'Inativar abrigos', 'module' => 'shelter', 'action' => 'deactivate'],
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
