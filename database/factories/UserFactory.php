<?php

namespace Database\Factories;

use App\Modules\Admin\Models\Organization;
use App\Modules\Tenancy\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tenant = Tenant::query()->first() ?? Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'legal_name' => 'Tenant de Teste',
            'trade_name' => 'Tenant Teste',
            'tenant_type' => 'MUNICIPAL',
            'document_number' => fake()->numerify('##########'),
            'state_code' => 'PA',
            'city_name' => 'Belém',
            'plan_type' => 'ESSENCIAL',
            'subscription_status' => 'ATIVA',
            'is_active' => true,
        ]);

        $organization = Organization::query()
            ->where('tenant_id', $tenant->id)
            ->first() ?? Organization::query()->create([
                'tenant_id' => $tenant->id,
                'name' => 'Defesa Civil Teste',
                'organization_type' => 'COMPDEC',
                'is_active' => true,
            ]);

        return [
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'unit_id' => null,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'cpf_hash' => null,
            'password_hash' => static::$password ??= Hash::make('password'),
            'phone' => fake()->phoneNumber(),
            'position_name' => 'Analista',
            'status' => 'ATIVO',
            'last_login_at' => null,
            'remember_token' => Str::random(10),
        ];
    }

}
