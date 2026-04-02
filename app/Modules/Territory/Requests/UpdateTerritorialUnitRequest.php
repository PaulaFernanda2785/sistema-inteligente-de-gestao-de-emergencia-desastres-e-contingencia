<?php

namespace App\Modules\Territory\Requests;

use App\Modules\Territory\Models\TerritorialUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTerritorialUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        $target = $this->route('unit');

        return $target instanceof TerritorialUnit
            ? ($this->user()?->can('update', $target) ?? false)
            : false;
    }

    public function rules(): array
    {
        $tenantId = (int) $this->user()->tenant_id;
        $territoryId = (int) $this->input('territory_id');
        $currentUnit = $this->route('unit');
        $currentUnitId = $currentUnit instanceof TerritorialUnit ? (int) $currentUnit->id : 0;

        return [
            'territory_id' => [
                'required',
                'integer',
                Rule::exists('territories', 'id')->where(
                    fn ($query) => $query->where('tenant_id', $tenantId),
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
                function (string $attribute, mixed $value, \Closure $fail) use ($currentUnitId): void {
                    if ($value !== null && (int) $value === $currentUnitId) {
                        $fail('A unidade pai nao pode ser a propria unidade.');
                    }
                },
            ],
            'name' => ['required', 'string', 'max:200'],
            'unit_type' => ['required', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:50'],
            'population_estimate' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
