<?php

namespace App\Modules\Territory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Municipio extends Model
{
    public $timestamps = false;

    protected $table = 'municipios';

    protected $fillable = [
        'estado_id',
        'nome',
        'codigo_ibge',
        'uf',
        'ativo',
    ];

    protected $casts = [
        'estado_id' => 'integer',
        'ativo' => 'boolean',
    ];

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function bairros(): HasMany
    {
        return $this->hasMany(Bairro::class, 'municipio_id');
    }

    public function territorialUnits(): HasMany
    {
        return $this->hasMany(TerritorialUnit::class, 'municipio_id');
    }
}
