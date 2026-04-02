<?php

namespace Tests\Feature;

use App\Modules\Admin\Models\Organization;
use App\Modules\Admin\Models\Permission;
use App\Modules\Admin\Models\Role;
use App\Modules\Admin\Models\User;
use App\Modules\Tenancy\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthAndUserFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_admin_users_flow_with_idempotency_protection(): void
    {
        $tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'legal_name' => 'Prefeitura Teste',
            'trade_name' => 'Defesa Civil Teste',
            'tenant_type' => 'MUNICIPAL',
            'document_number' => '00000000000000',
            'state_code' => 'PA',
            'city_name' => 'Belém',
            'plan_type' => 'ESSENCIAL',
            'subscription_status' => 'ATIVA',
            'is_active' => true,
        ]);

        $organization = Organization::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Coordenadoria Municipal de Defesa Civil',
            'organization_type' => 'COMPDEC',
            'is_active' => true,
        ]);

        $permissions = collect([
            ['code' => 'users.view', 'name' => 'Visualizar usuários', 'module' => 'admin.users', 'action' => 'view'],
            ['code' => 'users.create', 'name' => 'Criar usuários', 'module' => 'admin.users', 'action' => 'create'],
            ['code' => 'users.update', 'name' => 'Atualizar usuários', 'module' => 'admin.users', 'action' => 'update'],
            ['code' => 'users.deactivate', 'name' => 'Inativar usuários', 'module' => 'admin.users', 'action' => 'deactivate'],
            ['code' => 'organizations.view', 'name' => 'Visualizar organização', 'module' => 'admin.organizations', 'action' => 'view'],
            ['code' => 'organizations.update', 'name' => 'Atualizar organização', 'module' => 'admin.organizations', 'action' => 'update'],
        ])->map(fn (array $payload) => Permission::query()->create($payload));

        $adminRole = Role::query()->create([
            'tenant_id' => null,
            'code' => 'TENANT_ADMIN',
            'name' => 'Administrador do Tenant',
            'is_system_role' => true,
        ]);
        $adminRole->permissions()->sync($permissions->pluck('id')->all());

        $adminUser = User::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => 'Admin SIGEDC',
            'email' => 'admin@sigedc.local',
            'password_hash' => Hash::make('SenhaSegura@123'),
            'status' => 'ATIVO',
        ]);
        $adminUser->roles()->sync([$adminRole->id]);

        $loginResponse = $this->post('/login', [
            'email' => 'admin@sigedc.local',
            'password' => 'SenhaSegura@123',
            '_idempotency_token' => (string) Str::uuid(),
        ]);

        $loginResponse->assertRedirect(route('admin.users.index'));
        $this->assertAuthenticatedAs($adminUser);

        $listResponse = $this->getJson(route('admin.users.index'));
        $listResponse->assertOk();

        $idempotencyToken = (string) Str::uuid();
        $createPayload = [
            'name' => 'Usuário Operacional',
            'email' => 'operacional@sigedc.local',
            'organization_id' => $organization->id,
            'unit_id' => null,
            'phone' => '91999999999',
            'position_name' => 'Operador',
            'status' => 'ATIVO',
            'password' => 'SenhaSegura@123',
            'roles' => [$adminRole->id],
            '_idempotency_token' => $idempotencyToken,
        ];

        $firstCreate = $this->postJson(route('admin.users.store'), $createPayload);
        $firstCreate->assertCreated();

        $duplicateCreate = $this->postJson(route('admin.users.store'), $createPayload);
        $duplicateCreate->assertStatus(409);
        $duplicateCreate->assertJsonPath('code', 'idempotency_rejected');
    }
}
