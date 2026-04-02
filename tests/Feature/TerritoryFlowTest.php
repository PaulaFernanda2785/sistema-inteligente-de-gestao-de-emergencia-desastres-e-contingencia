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

class TerritoryFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_territory_and_units_flow_with_hierarchy_validation(): void
    {
        $tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'legal_name' => 'Prefeitura Teste',
            'trade_name' => 'Defesa Civil Teste',
            'tenant_type' => 'MUNICIPAL',
            'document_number' => '00000000000000',
            'state_code' => 'PA',
            'city_name' => 'Belem',
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
            ['code' => 'territories.view', 'name' => 'Visualizar territorios', 'module' => 'territory.territories', 'action' => 'view'],
            ['code' => 'territories.create', 'name' => 'Criar territorios', 'module' => 'territory.territories', 'action' => 'create'],
            ['code' => 'territories.update', 'name' => 'Atualizar territorios', 'module' => 'territory.territories', 'action' => 'update'],
            ['code' => 'territorial_units.view', 'name' => 'Visualizar unidades territoriais', 'module' => 'territory.units', 'action' => 'view'],
            ['code' => 'territorial_units.create', 'name' => 'Criar unidades territoriais', 'module' => 'territory.units', 'action' => 'create'],
            ['code' => 'territorial_units.update', 'name' => 'Atualizar unidades territoriais', 'module' => 'territory.units', 'action' => 'update'],
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

        $territoryCreate = $this->postJson(route('territory.territories.store'), [
            'name' => 'Fortaleza',
            'territory_type' => 'MUNICIPAL',
            'state_code' => 'CE',
            'ibge_code' => '2304400',
            '_idempotency_token' => (string) Str::uuid(),
        ]);
        $territoryCreate->assertCreated();
        $territoryId = (int) $territoryCreate->json('data.id');

        $rootUnitCreate = $this->postJson(route('territory.units.store'), [
            'territory_id' => $territoryId,
            'name' => 'Regional 1',
            'unit_type' => 'REGIONAL',
            'population_estimate' => 1000,
            '_idempotency_token' => (string) Str::uuid(),
        ]);
        $rootUnitCreate->assertCreated();
        $rootUnitId = (int) $rootUnitCreate->json('data.id');

        $childUnitCreate = $this->postJson(route('territory.units.store'), [
            'territory_id' => $territoryId,
            'parent_unit_id' => $rootUnitId,
            'name' => 'Bairro Centro',
            'unit_type' => 'BAIRRO',
            '_idempotency_token' => (string) Str::uuid(),
        ]);
        $childUnitCreate->assertCreated();
        $childUnitId = (int) $childUnitCreate->json('data.id');

        $cycleAttempt = $this->putJson(route('territory.units.update', ['unit' => $rootUnitId]), [
            'territory_id' => $territoryId,
            'parent_unit_id' => $childUnitId,
            'name' => 'Regional 1',
            'unit_type' => 'REGIONAL',
            '_idempotency_token' => (string) Str::uuid(),
        ]);
        $cycleAttempt->assertStatus(422);
        $cycleAttempt->assertJsonValidationErrors(['parent_unit_id']);

        $indexHtml = $this->get(route('territory.territories.index'));
        $indexHtml->assertOk();
        $indexHtml->assertSee('Base Territorial - Territorios');

        $indexJson = $this->getJson(route('territory.units.index'));
        $indexJson->assertOk();
        $indexJson->assertJsonPath('data.0.id', $rootUnitId);
    }
}
