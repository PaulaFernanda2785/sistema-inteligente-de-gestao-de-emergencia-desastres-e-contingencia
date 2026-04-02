<?php

namespace App\Modules\Shelter\Services;

use App\Core\Services\BaseService;
use App\Modules\Shelter\Models\Shelter;
use App\Modules\Territory\Models\TerritorialUnit;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class ShelterService extends BaseService
{
    public function create(array $data): Shelter
    {
        return DB::transaction(function () use ($data): Shelter {
            $tenantId = $this->tenantIdOrFail();
            $this->resolveTenantUnit($tenantId, (int) $data['territorial_unit_id']);

            $shelter = Shelter::query()->create([
                'tenant_id' => $tenantId,
                'territorial_unit_id' => (int) $data['territorial_unit_id'],
                'name' => $data['name'],
                'shelter_type' => $data['shelter_type'],
                'address' => $data['address'],
                'manager_name' => $data['manager_name'] ?? null,
                'contact_phone' => $data['contact_phone'] ?? null,
                'max_people_capacity' => (int) $data['max_people_capacity'],
                'accessibility_features' => $data['accessibility_features'] ?? null,
                'kitchen_available' => (bool) $data['kitchen_available'],
                'water_supply_available' => (bool) $data['water_supply_available'],
                'energy_supply_available' => (bool) $data['energy_supply_available'],
                'sanitary_structure_description' => $data['sanitary_structure_description'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'is_active' => (bool) $data['is_active'],
            ]);

            $this->auditLogger->log(
                module: 'shelter',
                action: 'create',
                entityType: Shelter::class,
                entityId: $shelter->id,
                newValues: $shelter->toArray(),
            );

            return $shelter->fresh(['territorialUnit:id,name']);
        });
    }

    public function update(Shelter $shelter, array $data): Shelter
    {
        return DB::transaction(function () use ($shelter, $data): Shelter {
            $tenantId = $this->tenantIdOrFail();
            $this->assertSameTenant((int) $shelter->tenant_id, $tenantId);
            $this->resolveTenantUnit($tenantId, (int) $data['territorial_unit_id']);

            $oldValues = $shelter->toArray();

            $shelter->update([
                'territorial_unit_id' => (int) $data['territorial_unit_id'],
                'name' => $data['name'],
                'shelter_type' => $data['shelter_type'],
                'address' => $data['address'],
                'manager_name' => $data['manager_name'] ?? null,
                'contact_phone' => $data['contact_phone'] ?? null,
                'max_people_capacity' => (int) $data['max_people_capacity'],
                'accessibility_features' => $data['accessibility_features'] ?? null,
                'kitchen_available' => (bool) $data['kitchen_available'],
                'water_supply_available' => (bool) $data['water_supply_available'],
                'energy_supply_available' => (bool) $data['energy_supply_available'],
                'sanitary_structure_description' => $data['sanitary_structure_description'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'is_active' => (bool) $data['is_active'],
            ]);

            $this->auditLogger->log(
                module: 'shelter',
                action: 'update',
                entityType: Shelter::class,
                entityId: $shelter->id,
                oldValues: $oldValues,
                newValues: $shelter->fresh()->toArray(),
            );

            return $shelter->fresh(['territorialUnit:id,name']);
        });
    }

    public function deactivate(Shelter $shelter): Shelter
    {
        return DB::transaction(function () use ($shelter): Shelter {
            $tenantId = $this->tenantIdOrFail();
            $this->assertSameTenant((int) $shelter->tenant_id, $tenantId);

            if (!$shelter->is_active) {
                return $shelter->fresh(['territorialUnit:id,name']);
            }

            $oldValues = $shelter->toArray();
            $shelter->update(['is_active' => false]);

            $this->auditLogger->log(
                module: 'shelter',
                action: 'deactivate',
                entityType: Shelter::class,
                entityId: $shelter->id,
                oldValues: $oldValues,
                newValues: $shelter->fresh()->toArray(),
            );

            return $shelter->fresh(['territorialUnit:id,name']);
        });
    }

    private function resolveTenantUnit(int $tenantId, int $territorialUnitId): TerritorialUnit
    {
        return TerritorialUnit::query()
            ->where('tenant_id', $tenantId)
            ->findOrFail($territorialUnitId);
    }

    private function assertSameTenant(int $resourceTenantId, int $tenantId): void
    {
        if ($resourceTenantId !== $tenantId) {
            throw new AuthorizationException('Operacao nao permitida para este tenant.');
        }
    }
}
