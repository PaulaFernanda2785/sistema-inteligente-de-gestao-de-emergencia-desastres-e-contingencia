<?php

namespace App\Modules\Tenancy\Models;

use App\Modules\Admin\Models\Organization;
use App\Modules\Admin\Models\Role;
use App\Modules\Admin\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tenant extends Model
{
    protected $table = 'tenants';

    protected $fillable = [
        'uuid',
        'legal_name',
        'trade_name',
        'tenant_type',
        'document_number',
        'state_code',
        'city_name',
        'plan_type',
        'subscription_status',
        'contract_start_date',
        'contract_end_date',
        'is_active',
    ];

    protected $casts = [
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Tenant $tenant): void {
            if (empty($tenant->uuid)) {
                $tenant->uuid = (string) Str::uuid();
            }
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }
}
