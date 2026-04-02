<?php

namespace App\Modules\Risk\Services;

use App\Core\Services\BaseService;
use App\Modules\Risk\Models\RiskArea;
use App\Modules\Territory\Models\TerritorialUnit;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class RiskAreaService extends BaseService
{
    public function create(array $data): RiskArea
    {
        return DB::transaction(function () use ($data): RiskArea {
            $tenantId = $this->tenantIdOrFail();
            $this->resolveTenantUnit($tenantId, (int) $data['territorial_unit_id']);

            $riskArea = RiskArea::query()->create([
                'tenant_id' => $tenantId,
                'territorial_unit_id' => (int) $data['territorial_unit_id'],
                'name' => $data['name'],
                'risk_type' => $data['risk_type'],
                'priority_level' => $data['priority_level'],
                'exposed_population_estimate' => $data['exposed_population_estimate'] ?? null,
                'description' => $data['description'] ?? null,
                'monitoring_notes' => $data['monitoring_notes'] ?? null,
                'is_active' => (bool) $data['is_active'],
            ]);

            $this->auditLogger->log(
                module: 'risk.areas',
                action: 'create',
                entityType: RiskArea::class,
                entityId: $riskArea->id,
                newValues: $riskArea->toArray(),
            );

            return $riskArea->fresh(['territorialUnit:id,name']);
        });
    }

    public function update(RiskArea $riskArea, array $data): RiskArea
    {
        return DB::transaction(function () use ($riskArea, $data): RiskArea {
            $tenantId = $this->tenantIdOrFail();
            $this->assertSameTenant((int) $riskArea->tenant_id, $tenantId);
            $this->resolveTenantUnit($tenantId, (int) $data['territorial_unit_id']);

            $oldValues = $riskArea->toArray();

            $riskArea->update([
                'territorial_unit_id' => (int) $data['territorial_unit_id'],
                'name' => $data['name'],
                'risk_type' => $data['risk_type'],
                'priority_level' => $data['priority_level'],
                'exposed_population_estimate' => $data['exposed_population_estimate'] ?? null,
                'description' => $data['description'] ?? null,
                'monitoring_notes' => $data['monitoring_notes'] ?? null,
                'is_active' => (bool) $data['is_active'],
            ]);

            $this->auditLogger->log(
                module: 'risk.areas',
                action: 'update',
                entityType: RiskArea::class,
                entityId: $riskArea->id,
                oldValues: $oldValues,
                newValues: $riskArea->fresh()->toArray(),
            );

            return $riskArea->fresh(['territorialUnit:id,name']);
        });
    }

    public function deactivate(RiskArea $riskArea): RiskArea
    {
        return DB::transaction(function () use ($riskArea): RiskArea {
            $tenantId = $this->tenantIdOrFail();
            $this->assertSameTenant((int) $riskArea->tenant_id, $tenantId);

            if (!$riskArea->is_active) {
                return $riskArea->fresh(['territorialUnit:id,name']);
            }

            $oldValues = $riskArea->toArray();
            $riskArea->update(['is_active' => false]);

            $this->auditLogger->log(
                module: 'risk.areas',
                action: 'deactivate',
                entityType: RiskArea::class,
                entityId: $riskArea->id,
                oldValues: $oldValues,
                newValues: $riskArea->fresh()->toArray(),
            );

            return $riskArea->fresh(['territorialUnit:id,name']);
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
