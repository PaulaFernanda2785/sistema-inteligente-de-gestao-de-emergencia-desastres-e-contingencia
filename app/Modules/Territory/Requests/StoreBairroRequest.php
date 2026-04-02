<?php

namespace App\Modules\Territory\Requests;

use App\Modules\Territory\Models\Bairro;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBairroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Bairro::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'municipio_id' => [
                'required',
                'integer',
                Rule::exists('municipios', 'id')->where(fn ($query) => $query->where('ativo', true)),
            ],
            'nome' => [
                'required',
                'string',
                'max:150',
                Rule::unique('bairros', 'nome')
                    ->where(fn ($query) => $query->where('municipio_id', (int) $this->input('municipio_id'))),
            ],
            'codigo_ibge' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('bairros', 'codigo_ibge'),
            ],
            'geojson_referencia' => ['nullable', 'string'],
            'ativo' => ['nullable', 'boolean'],
        ];
    }
}
