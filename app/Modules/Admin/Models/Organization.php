<?php

namespace App\Modules\Admin\Models;

use App\Core\Tenancy\Concerns\BelongsToTenant;
use App\Modules\Tenancy\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use BelongsToTenant;

    protected $table = 'organizations';

    protected $fillable = [
        'tenant_id',
        'name',
        'acronym',
        'organization_type',
        'address',
        'email',
        'phone',
        'coordinator_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(OrganizationalUnit::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
