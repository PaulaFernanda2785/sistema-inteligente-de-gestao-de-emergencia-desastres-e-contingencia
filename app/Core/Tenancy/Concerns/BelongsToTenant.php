<?php

namespace App\Core\Tenancy\Concerns;

use App\Core\Support\TenantContext;
use App\Core\Tenancy\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model): void {
            /** @var TenantContext $tenantContext */
            $tenantContext = app(TenantContext::class);
            $tenantId = $tenantContext->tenantId();

            if ($tenantId !== null && empty($model->tenant_id)) {
                $model->tenant_id = $tenantId;
            }
        });
    }

    public function scopeWithoutTenantScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope(TenantScope::class);
    }
}

