<?php

namespace App\Core\Support;

use App\Modules\Audit\Models\AuditLog;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Throwable;

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
        try {
            AuditLog::query()->create([
                'tenant_id' => $this->resolveTenantId(),
                'user_id' => Auth::id(),
                'event_type' => $this->limitLength($eventType, 50),
                'module' => $this->limitLength($module, 100),
                'action' => $this->limitLength($action, 50),
                'entity_type' => $entityType !== null ? $this->limitLength($entityType, 100) : null,
                'entity_id' => $entityId,
                'old_values' => $this->sanitizePayload($oldValues),
                'new_values' => $this->enrichNewValues($newValues),
                'ip_address' => $this->limitLength((string) Request::ip(), 64),
                'user_agent' => $this->limitLength((string) Request::userAgent(), 65535),
                'created_at' => now(),
            ]);
        } catch (Throwable $exception) {
            Log::warning('audit_log_persist_failed', [
                'module' => $module,
                'action' => $action,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function resolveTenantId(): ?int
    {
        $contextTenantId = $this->tenantContext->tenantId();
        if ($contextTenantId !== null) {
            return $contextTenantId;
        }

        /** @var Authenticatable|null $user */
        $user = Auth::user();
        if ($user === null) {
            return null;
        }

        /** @var mixed $tenantId */
        $tenantId = $user->tenant_id ?? null;

        return is_numeric($tenantId) ? (int) $tenantId : null;
    }

    private function enrichNewValues(?array $newValues): array
    {
        $payload = $this->sanitizePayload($newValues) ?? [];
        $payload['_request'] = [
            'method' => Request::method(),
            'path' => Request::path(),
            'request_id' => Request::header('X-Request-Id'),
        ];

        return $payload;
    }

    private function sanitizePayload(?array $payload): ?array
    {
        if ($payload === null) {
            return null;
        }

        $sensitiveKeys = [
            'password',
            'password_hash',
            'remember_token',
            'token',
            '_token',
            '_idempotency_token',
        ];

        foreach ($payload as $key => $value) {
            if (is_string($key) && in_array(strtolower($key), $sensitiveKeys, true)) {
                unset($payload[$key]);
                continue;
            }

            if (is_array($value)) {
                $payload[$key] = $this->sanitizePayload($value);
                continue;
            }

            if (is_string($value)) {
                $payload[$key] = $this->limitLength($value, 2000);
            }
        }

        return $payload;
    }

    private function limitLength(string $value, int $limit): string
    {
        return mb_substr($value, 0, $limit);
    }
}
