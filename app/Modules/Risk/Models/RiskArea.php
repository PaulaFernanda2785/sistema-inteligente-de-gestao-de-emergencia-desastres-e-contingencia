<?php

namespace App\Modules\Risk\Models;

use App\Core\Tenancy\Concerns\BelongsToTenant;
use App\Modules\Tenancy\Models\Tenant;
use App\Modules\Territory\Models\TerritorialUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskArea extends Model
{
    use BelongsToTenant;

    public const RISK_TYPES = [
        'ALAGAMENTO',
        'ENXURRADA',
        'INUNDACAO',
        'DESLIZAMENTO',
        'SECA',
        'VENDAVAL',
        'INCENDIO',
        'OUTRO',
    ];

    public const PRIORITY_LEVELS = [
        'BAIXA',
        'MEDIA',
        'ALTA',
        'CRITICA',
    ];

    protected $table = 'risk_areas';

    protected $fillable = [
        'tenant_id',
        'territorial_unit_id',
        'name',
        'risk_type',
        'priority_level',
        'exposed_population_estimate',
        'description',
        'monitoring_notes',
        'is_active',
    ];

    protected $casts = [
        'exposed_population_estimate' => 'integer',
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
