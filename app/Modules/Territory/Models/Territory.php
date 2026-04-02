<?php

namespace App\Modules\Territory\Models;

use App\Core\Tenancy\Concerns\BelongsToTenant;
use App\Modules\Tenancy\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Territory extends Model
{
    use BelongsToTenant;

    protected $table = 'territories';

    protected $fillable = [
        'tenant_id',
        'name',
        'territory_type',
        'ibge_code',
        'state_code',
        'description',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(TerritorialUnit::class);
    }
}
