<?php

namespace App\Modules\Territory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bairro extends Model
{
    public $timestamps = false;

    protected $table = 'bairros';

    protected $fillable = [
        'municipio_id',
        'nome',
        'codigo_ibge',
        'geojson_referencia',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    public function territorialUnits(): HasMany
    {
        return $this->hasMany(TerritorialUnit::class, 'bairro_id');
    }
}
