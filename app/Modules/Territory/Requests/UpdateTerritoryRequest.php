<?php

namespace App\Modules\Territory\Requests;

use App\Modules\Territory\Models\Territory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTerritoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $target = $this->route('territory');

        return $target instanceof Territory
            ? ($this->user()?->can('update', $target) ?? false)
            : false;
    }

    protected function prepareForValidation(): void
    {
        $stateCode = $this->input('state_code');
        if (is_string($stateCode)) {
            $this->merge(['state_code' => strtoupper(trim($stateCode))]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:200'],
            'territory_type' => ['required', 'string', 'max:100'],
            'ibge_code' => ['nullable', 'string', 'max:20'],
            'state_code' => ['required', 'string', 'size:2', Rule::in($this->allowedStateCodes())],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<int, string>
     */
    private function allowedStateCodes(): array
    {
        return [
            'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO',
            'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI',
            'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO',
        ];
    }
}
