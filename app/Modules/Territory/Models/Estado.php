<?php

namespace App\Modules\Territory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estado extends Model
{
    public $timestamps = false;

    protected $table = 'estados';

    protected $fillable = [
        'nome',
        'sigla',
        'codigo_ibge',
        'regiao',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function municipios(): HasMany
    {
        return $this->hasMany(Municipio::class, 'estado_id');
    }
}
