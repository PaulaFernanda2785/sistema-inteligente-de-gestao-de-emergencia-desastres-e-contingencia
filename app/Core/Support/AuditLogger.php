<?php

namespace App\Core\Support;

use App\Modules\Audit\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    public function __construct(
        private readonly TenantContext $tenantContext,
    ) {
    }

    public function log(
        string $module,
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        string $eventType = 'system_action',
        ?array $oldValues = null,
        ?array $newValues = null,
    ): void {
        AuditLog::query()->create([
            'tenant_id' => $this->tenantContext->tenantId(),
            'user_id' => Auth::id(),
            'event_type' => $eventType,
            'module' => $module,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }
}
