<?php

namespace App\Modules\Shelter\Models;

use App\Core\Tenancy\Concerns\BelongsToTenant;
use App\Modules\Tenancy\Models\Tenant;
use App\Modules\Territory\Models\TerritorialUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shelter extends Model
{
    use BelongsToTenant;

    public const SHELTER_TYPES = [
        'ESCOLA',
        'GINASIO',
        'IGREJA',
        'CENTRO_COMUNITARIO',
        'UNIDADE_PUBLICA',
        'OUTRO',
    ];

    protected $table = 'shelters';

    protected $fillable = [
        'tenant_id',
        'territorial_unit_id',
        'name',
        'shelter_type',
        'address',
        'manager_name',
        'contact_phone',
        'max_people_capacity',
        'accessibility_features',
        'kitchen_available',
        'water_supply_available',
        'energy_supply_available',
        'sanitary_structure_description',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected $casts = [
        'max_people_capacity' => 'integer',
        'kitchen_available' => 'boolean',
        'water_supply_available' => 'boolean',
        'energy_supply_available' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function territorialUnit(): BelongsTo
    {
        return $this->belongsTo(TerritorialUnit::class, 'territorial_unit_id');
    }
}
