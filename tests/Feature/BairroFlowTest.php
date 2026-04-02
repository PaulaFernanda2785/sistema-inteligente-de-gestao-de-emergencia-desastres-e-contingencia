<?php

namespace Tests\Feature;

use App\Modules\Admin\Models\Organization;
use App\Modules\Admin\Models\Permission;
use App\Modules\Admin\Models\Role;
use App\Modules\Admin\Models\User;
use App\Modules\Territory\Models\Municipio;
use App\Modules\Tenancy\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class BairroFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_bairro_crud_with_municipio_relationship(): void
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
            ['code' => 'bairros.view', 'name' => 'Visualizar bairros', 'module' => 'territory.bairros', 'action' => 'view'],
            ['code' => 'bairros.create', 'name' => 'Criar bairros', 'module' => 'territory.bairros', 'action' => 'create'],
            ['code' => 'bairros.update', 'name' => 'Atualizar bairros', 'module' => 'territory.bairros', 'action' => 'update'],
            ['code' => 'shelters.view', 'name' => 'Visualizar abrigos', 'module' => 'shelter', 'action' => 'view'],
        ])->map(fn (array $payload) => Permission::query()->create($payload));

        $role = Role::query()->create([
            'tenant_id' => null,
            'code' => 'TENANT_ADMIN',
            'name' => 'Administrador do Tenant',
            'is_system_role' => true,
        ]);
        $role->permissions()->sync($permissions->pluck('id')->all());

        $user = User::query()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => 'Admin SIGEDC',
            'email' => 'admin@sigedc.local',
            'password_hash' => Hash::make('SenhaSegura@123'),
            'status' => 'ATIVO',
        ]);
        $user->roles()->sync([$role->id]);

        $this->post('/login', [
            'email' => 'admin@sigedc.local',
            'password' => 'SenhaSegura@123',
            '_idempotency_token' => (string) Str::uuid(),
        ])->assertRedirect(route('shelters.index'));

        $belem = Municipio::query()->create([
            'nome' => 'Belem',
            'codigo_ibge' => '1501402',
            'uf' => 'PA',
            'ativo' => true,
        ]);

        $ananindeua = Municipio::query()->create([
            'nome' => 'Ananindeua',
            'codigo_ibge' => '1500800',
            'uf' => 'PA',
            'ativo' => true,
        ]);

        $createResponse = $this->postJson(route('territory.bairros.store'), [
            'municipio_id' => $belem->id,
            'nome' => 'Marco',
            'codigo_ibge' => '1501402-MARCO',
            'ativo' => true,
            '_idempotency_token' => (string) Str::uuid(),
        ]);

        $createResponse->assertCreated();
        $bairroId = (int) $createResponse->json('data.id');

        $updateResponse = $this->putJson(route('territory.bairros.update', $bairroId), [
            'municipio_id' => $ananindeua->id,
            'nome' => 'Cidade Nova',
            'codigo_ibge' => '1500800-CN',
            'ativo' => true,
            '_idempotency_token' => (string) Str::uuid(),
        ]);

        $updateResponse->assertOk();
        $updateResponse->assertJsonPath('data.municipio_id', $ananindeua->id);
        $updateResponse->assertJsonPath('data.nome', 'Cidade Nova');

        $duplicateResponse = $this->postJson(route('territory.bairros.store'), [
            'municipio_id' => $ananindeua->id,
            'nome' => 'Cidade Nova',
            '_idempotency_token' => (string) Str::uuid(),
        ]);

        $duplicateResponse->assertStatus(422);
        $duplicateResponse->assertJsonValidationErrors(['nome']);

        $listResponse = $this->getJson(route('territory.bairros.index', ['municipio_id' => $ananindeua->id]));
        $listResponse->assertOk();
        $listResponse->assertJsonPath('data.0.id', $bairroId);
    }
}
