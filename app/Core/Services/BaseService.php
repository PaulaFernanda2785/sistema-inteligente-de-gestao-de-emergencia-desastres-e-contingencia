<?php

namespace App\Core\Services;

use App\Core\Support\AuditLogger;
use App\Core\Support\TenantContext;
use Illuminate\Auth\Access\AuthorizationException;

abstract class BaseService
{
    public function __construct(
        protected TenantContext $tenantContext,
        protected AuditLogger $auditLogger,
    ) {
    }

    protected function tenantIdOrFail(): int
    {
        $tenantId = $this->tenantContext->tenantId();

        if ($tenantId === null) {
            throw new AuthorizationException('Tenant não resolvido para esta requisição.');
        }

        return $tenantId;
    }
}
