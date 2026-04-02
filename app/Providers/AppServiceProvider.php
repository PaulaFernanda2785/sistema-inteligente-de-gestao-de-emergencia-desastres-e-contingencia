<?php

namespace App\Providers;

use App\Core\Support\TenantContext;
use App\Modules\Admin\Models\Organization;
use App\Modules\Admin\Models\User;
use App\Modules\Admin\Policies\OrganizationPolicy;
use App\Modules\Admin\Policies\UserPolicy;
use App\Modules\Risk\Models\RiskArea;
use App\Modules\Risk\Policies\RiskAreaPolicy;
use App\Modules\Shelter\Models\Shelter;
use App\Modules\Shelter\Policies\ShelterPolicy;
use App\Modules\Territory\Models\Bairro;
use App\Modules\Territory\Models\TerritorialUnit;
use App\Modules\Territory\Models\Territory;
use App\Modules\Territory\Policies\BairroPolicy;
use App\Modules\Territory\Policies\TerritorialUnitPolicy;
use App\Modules\Territory\Policies\TerritoryPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TenantContext::class, fn (): TenantContext => new TenantContext());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Organization::class, OrganizationPolicy::class);
        Gate::policy(Territory::class, TerritoryPolicy::class);
        Gate::policy(TerritorialUnit::class, TerritorialUnitPolicy::class);
        Gate::policy(Bairro::class, BairroPolicy::class);
        Gate::policy(RiskArea::class, RiskAreaPolicy::class);
        Gate::policy(Shelter::class, ShelterPolicy::class);

        Gate::before(function (User $user): bool|null {
            $user->loadMissing('tenant');

            if ($user->status !== 'ATIVO') {
                return false;
            }

            if ($user->tenant === null || !$user->tenant->is_active) {
                return false;
            }

            return null;
        });
    }
}
