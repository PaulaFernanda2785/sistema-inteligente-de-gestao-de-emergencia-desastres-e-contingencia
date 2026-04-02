<?php

namespace App\Modules\Tenancy\Middleware;

use App\Core\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantContext
{
    public function __construct(
        private readonly TenantContext $tenantContext,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || $user->tenant === null || !$user->tenant->is_active) {
            abort(Response::HTTP_FORBIDDEN, 'Tenant inválido ou inativo.');
        }

        $this->tenantContext->setTenant($user->tenant);

        return $next($request);
    }
}
