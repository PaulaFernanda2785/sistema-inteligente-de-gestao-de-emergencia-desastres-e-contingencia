<?php

namespace App\Modules\Territory\Services;

use App\Core\Services\BaseService;
use App\Modules\Territory\Models\TerritorialUnit;
use App\Modules\Territory\Models\Territory;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TerritoryService extends BaseService
{
    public function createTerritory(array $data): Territory
    {
        return DB::transaction(function () use ($data): Territory {
            $tenantId = $this->tenantIdOrFail();

            $territory = Territory::query()->create([
                'tenant_id' => $tenantId,
                'name' => $data['name'],
                'territory_type' => $data['territory_type'],
                'ibge_code' => $data['ibge_code'] ?? null,
                'state_code' => strtoupper($data['state_code']),
                'description' => $data['description'] ?? null,
            ]);

            $this->auditLogger->log(
                module: 'territory.territories',
                action: 'create',
                entityType: Territory::class,
                entityId: $territory->id,
                newValues: $territory->toArray(),
            );

            return $territory->fresh(['tenant:id,trade_name']);
        });
    }

    public function updateTerritory(Territory $territory, array $data): Territory
    {
        return DB::transaction(function () use ($territory, $data): Territory {
            $tenantId = $this->tenantIdOrFail();
            $this->assertSameTenant($territory->tenant_id, $tenantId);

            $oldValues = $territory->toArray();

            $territory->update([
                'name' => $data['name'],
                'territory_type' => $data['territory_type'],
                'ibge_code' => $data['ibge_code'] ?? null,
                'state_code' => strtoupper($data['state_code']),
                'description' => $data['description'] ?? null,
            ]);

            $this->auditLogger->log(
                module: 'territory.territories',
                action: 'update',
                entityType: Territory::class,
                entityId: $territory->id,
                oldValues: $oldValues,
                newValues: $territory->fresh()->toArray(),
            );

            return $territory->fresh(['tenant:id,trade_name']);
        });
    }

    public function createUnit(array $data): TerritorialUnit
    {
        return DB::transaction(function () use ($data): TerritorialUnit {
            $tenantId = $this->tenantIdOrFail();
            $territory = $this->resolveTenantTerritory($tenantId, (int) $data['territory_id']);

            $parentId = isset($data['parent_unit_id']) ? (int) $data['parent_unit_id'] : null;
            if ($parentId !== null) {
                $this->resolveValidParent(
                    tenantId: $tenantId,
                    territoryId: $territory->id,
                    parentUnitId: $parentId,
                    currentUnitId: null,
                );
            }

            $unit = TerritorialUnit::query()->create([
                'tenant_id' => $tenantId,
                'territory_id' => $territory->id,
                'parent_unit_id' => $parentId,
                'name' => $data['name'],
                'unit_type' => $data['unit_type'],
                'code' => $data['code'] ?? null,
                'population_estimate' => $data['population_estimate'] ?? null,
            ]);

            $this->auditLogger->log(
                module: 'territory.units',
                action: 'create',
                entityType: TerritorialUnit::class,
                entityId: $unit->id,
                newValues: $unit->toArray(),
            );

            return $unit->fresh(['territory:id,name', 'parent:id,name']);
        });
    }

    public function updateUnit(TerritorialUnit $unit, array $data): TerritorialUnit
    {
        return DB::transaction(function () use ($unit, $data): TerritorialUnit {
            $tenantId = $this->tenantIdOrFail();
            $this->assertSameTenant($unit->tenant_id, $tenantId);

            $territory = $this->resolveTenantTerritory($tenantId, (int) $data['territory_id']);
            $parentId = isset($data['parent_unit_id']) ? (int) $data['parent_unit_id'] : null;

            if ($parentId !== null) {
                $this->resolveValidParent(
                    tenantId: $tenantId,
                    territoryId: $territory->id,
                    parentUnitId: $parentId,
                    currentUnitId: $unit->id,
                );
            }

            $oldValues = $unit->toArray();

            $unit->update([
                'territory_id' => $territory->id,
                'parent_unit_id' => $parentId,
                'name' => $data['name'],
                'unit_type' => $data['unit_type'],
                'code' => $data['code'] ?? null,
                'population_estimate' => $data['population_estimate'] ?? null,
            ]);

            $this->auditLogger->log(
                module: 'territory.units',
                action: 'update',
                entityType: TerritorialUnit::class,
                entityId: $unit->id,
                oldValues: $oldValues,
                newValues: $unit->fresh()->toArray(),
            );

            return $unit->fresh(['territory:id,name', 'parent:id,name']);
        });
    }

    private function resolveTenantTerritory(int $tenantId, int $territoryId): Territory
    {
        return Territory::query()
            ->where('tenant_id', $tenantId)
            ->findOrFail($territoryId);
    }

    private function resolveValidParent(
        int $tenantId,
        int $territoryId,
        int $parentUnitId,
        ?int $currentUnitId,
    ): TerritorialUnit {
        if ($currentUnitId !== null && $parentUnitId === $currentUnitId) {
            throw ValidationException::withMessages([
                'parent_unit_id' => 'A unidade pai nao pode ser a propria unidade.',
            ]);
        }

        $parent = TerritorialUnit::query()
            ->where('tenant_id', $tenantId)
            ->where('territory_id', $territoryId)
            ->find($parentUnitId);

        if ($parent === null) {
            throw ValidationException::withMessages([
                'parent_unit_id' => 'A unidade pai deve pertencer ao mesmo territorio.',
            ]);
        }

        if ($currentUnitId !== null) {
            $this->assertNoHierarchyCycle($tenantId, $currentUnitId, $parent->id);
        }

        return $parent;
    }

    private function assertNoHierarchyCycle(int $tenantId, int $currentUnitId, int $parentUnitId): void
    {
        $visited = [];
        $cursor = $parentUnitId;

        while ($cursor !== null) {
            if ($cursor === $currentUnitId) {
                throw ValidationException::withMessages([
                    'parent_unit_id' => 'Ciclo hierarquico detectado para a unidade territorial.',
                ]);
            }

            if (isset($visited[$cursor])) {
                throw ValidationException::withMessages([
                    'parent_unit_id' => 'Ciclo hierarquico detectado para a unidade territorial.',
                ]);
            }

            $visited[$cursor] = true;

            $parent = TerritorialUnit::query()
                ->where('tenant_id', $tenantId)
                ->find($cursor);

            $cursor = $parent?->parent_unit_id;
        }
    }

    private function assertSameTenant(int $resourceTenantId, int $tenantId): void
    {
        if ($resourceTenantId !== $tenantId) {
            throw new AuthorizationException('Operacao nao permitida para este tenant.');
        }
    }
}
