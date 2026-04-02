<?php

namespace Tests\Feature;

use App\Modules\Admin\Models\Organization;
use App\Modules\Admin\Models\Permission;
use App\Modules\Admin\Models\Role;
use App\Modules\Admin\Models\User;
use App\Modules\Tenancy\Models\Tenant;
use App\Modules\Territory\Models\TerritorialUnit;
use App\Modules\Territory\Models\Territory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class RiskAreaFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_risk_area_crud_and_tenant_validation(): void
    {
        [$tenant, $organization, $adminUser] = $this->bootstrapAdminContext(
            email: 'risk.admin@sigedc.local',
            tenantDocument: '11111111111111',
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
            'name' => 'Regional 1',
            'unit_type' => 'REGIONAL',
        ]);

        $otherTenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'legal_name' => 'Prefeitura Externa',
            'trade_name' => 'Defesa Civil Externa',
            'tenant_type' => 'MUNICIPAL',
            'document_number' => '22222222222222',
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

        $loginResponse = $this->post('/login', [
            'email' => $adminUser->email,
            'password' => 'SenhaSegura@123',
            '_idempotency_token' => (string) Str::uuid(),
        ]);
        $loginResponse->assertRedirect(route('shelters.index'));

        $createResponse = $this->postJson(route('risk.areas.store'), [
            'territorial_unit_id' => $unit->id,
            'name' => 'Area Centro Historico',
            'risk_type' => 'ALAGAMENTO',
            'priority_level' => 'ALTA',
            'exposed_population_estimate' => 950,
            'description' => 'Area com recorrencia de acumulacao de agua.',
            'monitoring_notes' => 'Monitorar chuva intensa.',
            'is_active' => true,
            '_idempotency_token' => (string) Str::uuid(),
        ]);

        $createResponse->assertCreated();
        $riskAreaId = (int) $createResponse->json('data.id');

        $updateResponse = $this->putJson(route('risk.areas.update', ['risk_area' => $riskAreaId]), [
            'territorial_unit_id' => $unit->id,
            'name' => 'Area Centro Historico Revisada',
            'risk_type' => 'INUNDACAO',
            'priority_level' => 'CRITICA',
            'exposed_population_estimate' => 1200,
            'description' => 'Atualizacao de criticidade.',
            'monitoring_notes' => 'Ativar alerta antecipado.',
            'is_active' => true,
            '_idempotency_token' => (string) Str::uuid(),
        ]);

        $updateResponse->assertOk();
        $updateResponse->assertJsonPath('data.priority_level', 'CRITICA');

        $invalidTenantUnitResponse = $this->postJson(route('risk.areas.store'), [
            'territorial_unit_id' => $otherUnit->id,
            'name' => 'Tentativa invalida',
            'risk_type' => 'ALAGAMENTO',
            'priority_level' => 'MEDIA',
            'is_active' => true,
            '_idempotency_token' => (string) Str::uuid(),
        ]);

        $invalidTenantUnitResponse->assertStatus(422);
        $invalidTenantUnitResponse->assertJsonValidationErrors(['territorial_unit_id']);

        $deactivateResponse = $this->patchJson(route('risk.areas.deactivate', ['risk_area' => $riskAreaId]), [
            '_idempotency_token' => (string) Str::uuid(),
        ]);
        $deactivateResponse->assertOk();
        $deactivateResponse->assertJsonPath('data.is_active', false);

        $indexHtml = $this->get(route('risk.areas.index'));
        $indexHtml->assertOk();
        $indexHtml->assertSee('Base Territorial - Areas de risco');
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
            ['code' => 'risk_areas.view', 'name' => 'Visualizar areas de risco', 'module' => 'risk.areas', 'action' => 'view'],
            ['code' => 'risk_areas.create', 'name' => 'Criar areas de risco', 'module' => 'risk.areas', 'action' => 'create'],
            ['code' => 'risk_areas.update', 'name' => 'Atualizar areas de risco', 'module' => 'risk.areas', 'action' => 'update'],
            ['code' => 'risk_areas.deactivate', 'name' => 'Inativar areas de risco', 'module' => 'risk.areas', 'action' => 'deactivate'],
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
