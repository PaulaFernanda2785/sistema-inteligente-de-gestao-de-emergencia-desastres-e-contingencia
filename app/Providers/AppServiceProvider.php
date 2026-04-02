<?php

namespace App\Providers;

use App\Core\Support\TenantContext;
use App\Modules\Admin\Models\Organization;
use App\Modules\Admin\Models\User;
use App\Modules\Admin\Policies\OrganizationPolicy;
use App\Modules\Admin\Policies\UserPolicy;
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
    }
}
