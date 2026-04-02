<?php

namespace App\Modules\Territory\Models;

use App\Core\Tenancy\Concerns\BelongsToTenant;
use App\Modules\Tenancy\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TerritorialUnit extends Model
{
    use BelongsToTenant;

    protected $table = 'territorial_units';

    protected $fillable = [
        'tenant_id',
        'territory_id',
        'parent_unit_id',
        'name',
        'unit_type',
        'code',
        'population_estimate',
    ];

    protected $casts = [
        'population_estimate' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function territory(): BelongsTo
    {
        return $this->belongsTo(Territory::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_unit_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_unit_id');
    }
}
