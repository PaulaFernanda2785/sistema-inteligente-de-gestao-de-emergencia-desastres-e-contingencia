<?php

namespace App\Modules\Territory\Requests;

use App\Modules\Territory\Models\TerritorialUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTerritorialUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', TerritorialUnit::class) ?? false;
    }

    public function rules(): array
    {
        $tenantId = (int) $this->user()->tenant_id;
        $territoryId = (int) $this->input('territory_id');

        return [
            'territory_id' => [
                'required',
                'integer',
                Rule::exists('territories', 'id')->where(
                    fn ($query) => $query->where('tenant_id', $tenantId),
                ),
            ],
            'municipio_id' => [
                'required',
                'integer',
                Rule::exists('municipios', 'id')->where(
                    fn ($query) => $query->where('ativo', true),
                ),
            ],
            'bairro_id' => [
                'nullable',
                'integer',
                Rule::exists('bairros', 'id')->where(
                    fn ($query) => $query
                        ->where('ativo', true)
                        ->where('municipio_id', (int) $this->input('municipio_id')),
                ),
            ],
            'parent_unit_id' => [
                'nullable',
                'integer',
                Rule::exists('territorial_units', 'id')->where(
                    fn ($query) => $query
                        ->where('tenant_id', $tenantId)
                        ->where('territory_id', $territoryId),
                ),
            ],
            'name' => ['required', 'string', 'max:200'],
            'unit_type' => ['required', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:50'],
            'population_estimate' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
