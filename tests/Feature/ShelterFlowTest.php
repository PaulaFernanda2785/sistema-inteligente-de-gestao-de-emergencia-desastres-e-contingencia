<?php

namespace Tests\Feature;

use App\Modules\Admin\Models\Organization;
use App\Modules\Admin\Models\Permission;
use App\Modules\Admin\Models\Role;
use App\Modules\Admin\Models\User;
use App\Modules\Shelter\Models\Shelter;
use App\Modules\Tenancy\Models\Tenant;
use App\Modules\Territory\Models\TerritorialUnit;
use App\Modules\Territory\Models\Territory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShelterFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_shelter_crud_and_tenant_isolation(): void
    {
        [$tenant, $organization, $adminUser] = $this->bootstrapAdminContext(
            email: 'shelter.admin@sigedc.local',
            tenantDocument: '33333333333333',
        );

        $territory = Territory::query()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Fortaleza',
            'territory_type' => 'MUNICIPAL',
            'state_code' => 'CE',
        ]);

        $unit = TerritorialUnit::query()->create([
            'tenant_id' => $tenant->id,
            'territory_id' => $territory->id,
            'name' => 'Regional Centro',
            'unit_type' => 'REGIONAL',
        ]);

        $otherTenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'legal_name' => 'Prefeitura Externa',
            'trade_name' => 'Defesa Civil Externa',
            'tenant_type' => 'MUNICIPAL',
            'document_number' => '44444444444444',
            'state_code' => 'CE',
            'city_name' => 'Aquiraz',
            'plan_type' => 'ESSENCIAL',
            'subscription_status' => 'ATIVA',
            'is_active' => true,
        ]);

        $otherTerritory = Territory::query()->create([
            'tenant_id' => $otherTenant->id,
            'name' => 'Aquiraz',
            'territory_type' => 'MUNICIPAL',
            'state_code' => 'CE',
        ]);

        $otherUnit = TerritorialUnit::query()->create([
            'tenant_id' => $otherTenant->id,
            'territory_id' => $otherTerritory->id,
            'name' => 'Regional Externa',
            'unit_type' => 'REGIONAL',
        ]);

        $this->post('/login', [
            'email' => $adminUser->email,
            'password' => 'SenhaSegura@123',
            '_idempotency_token' => (string) Str::uuid(),
        ])->assertRedirect(route('shelters.index'));

        $createResponse = $this->postJson(route('shelters.store'), [
            'territorial_unit_id' => $unit->id,
            'name' => 'Escola Municipal Centro',
            'shelter_type' => 'ESCOLA',
            'address' => 'Rua A, 100',
            'manager_name' => 'Joao Silva',
            'contact_phone' => '85999999999',
            'max_people_capacity' => 500,
            'accessibility_features' => 'Rampas e banheiros adaptados',
            'kitchen_available' => true,
            'water_supply_available' => true,
            'energy_supply_available' => true,
            'sanitary_structure_description' => '6 banheiros',
            'latitude' => -3.7304512,
            'longitude' => -38.5217989,
            'is_active' => true,
            '_idempotency_token' => (string) Str::uuid(),
        ]);

        $createResponse->assertCreated();
        $shelterId = (int) $createResponse->json('data.id');

        $updateResponse = $this->putJson(route('shelters.update', ['shelter' => $shelterId]), [
            'territorial_unit_id' => $unit->id,
            'name' => 'Escola Municipal Centro Revisada',
            'shelter_type' => 'UNIDADE_PUBLICA',
            'address' => 'Rua A, 120',
            'manager_name' => 'Maria Souza',
            'contact_phone' => '85888888888',
            'max_people_capacity' => 650,
            'accessibility_features' => 'Rampa, corrimao e banheiro adaptado',
            'kitchen_available' => true,
            'water_supply_available' => true,
            'energy_supply_available' => true,
            'sanitary_structure_description' => '8 banheiros',
            'latitude' => -3.7300000,
            'longitude' => -38.5200000,
            'is_active' => true,
            '_idempotency_token' => (string) Str::uuid(),
        ]);

        $updateResponse->assertOk();
        $updateResponse->assertJsonPath('data.max_people_capacity', 650);

        $invalidTenantUnitResponse = $this->postJson(route('shelters.store'), [
            'territorial_unit_id' => $otherUnit->id,
            'name' => 'Tentativa invalida',
            'shelter_type' => 'ESCOLA',
            'address' => 'Rua Invalida',
            'max_people_capacity' => 50,
            'kitchen_available' => false,
            'water_supply_available' => false,
            'energy_supply_available' => false,
            'is_active' => true,
            '_idempotency_token' => (string) Str::uuid(),
        ]);
        $invalidTenantUnitResponse->assertStatus(422);
        $invalidTenantUnitResponse->assertJsonValidationErrors(['territorial_unit_id']);

        $deactivateResponse = $this->patchJson(route('shelters.deactivate', ['shelter' => $shelterId]), [
            '_idempotency_token' => (string) Str::uuid(),
        ]);
        $deactivateResponse->assertOk();
        $deactivateResponse->assertJsonPath('data.is_active', false);

        $htmlResponse = $this->get(route('shelters.index'));
        $htmlResponse->assertOk();
        $htmlResponse->assertSee('Abrigos Potenciais');

        $this->assertFalse((bool) Shelter::query()->findOrFail($shelterId)->is_active);
    }

    /**
     * @return array{0: Tenant, 1: Organization, 2: User}
     */
    private function bootstrapAdminContext(string $email, string $tenantDocument): array
    {
        $tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'legal_name' => 'Prefeitura Teste',
            'trade_name' => 'Defesa Civil Teste',
            'tenant_type' => 'MUNICIPAL',
            'document_number' => $tenantDocument,
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
            ['code' => 'shelters.view', 'name' => 'Visualizar abrigos', 'module' => 'shelter', 'action' => 'view'],
            ['code' => 'shelters.create', 'name' => 'Criar abrigos', 'module' => 'shelter', 'action' => 'create'],
            ['code' => 'shelters.update', 'name' => 'Atualizar abrigos', 'module' => 'shelter', 'action' => 'update'],
            ['code' => 'shelters.deactivate', 'name' => 'Inativar abrigos', 'module' => 'shelter', 'action' => 'deactivate'],
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
            'email' => $email,
            'password_hash' => Hash::make('SenhaSegura@123'),
            'status' => 'ATIVO',
        ]);
        $adminUser->roles()->sync([$adminRole->id]);

        return [$tenant, $organization, $adminUser];
    }
}
