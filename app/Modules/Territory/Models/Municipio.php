<?php

namespace App\Modules\Territory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Municipio extends Model
{
    public $timestamps = false;

    protected $table = 'municipios';

    protected $fillable = [
        'nome',
        'codigo_ibge',
        'uf',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function bairros(): HasMany
    {
        return $this->hasMany(Bairro::class, 'municipio_id');
    }

    public function territorialUnits(): HasMany
    {
        return $this->hasMany(TerritorialUnit::class, 'municipio_id');
    }
}
