<?php

namespace App\Modules\Territory\Requests;

use App\Modules\Territory\Models\Bairro;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBairroRequest extends FormRequest
{
    public function authorize(): bool
    {
        $bairro = $this->route('bairro');

        return $bairro instanceof Bairro
            ? ($this->user()?->can('update', $bairro) ?? false)
            : false;
    }

    public function rules(): array
    {
        $bairro = $this->route('bairro');
        $bairroId = $bairro instanceof Bairro ? (int) $bairro->id : 0;

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
                    ->where(fn ($query) => $query->where('municipio_id', (int) $this->input('municipio_id')))
                    ->ignore($bairroId),
            ],
            'codigo_ibge' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('bairros', 'codigo_ibge')->ignore($bairroId),
            ],
            'geojson_referencia' => ['nullable', 'string'],
            'ativo' => ['nullable', 'boolean'],
        ];
    }
}
