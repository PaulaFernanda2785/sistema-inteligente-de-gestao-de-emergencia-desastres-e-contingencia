<?php

namespace App\Modules\Risk\Requests;

use App\Modules\Risk\Models\RiskArea;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRiskAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $target = $this->route('risk_area');

        return $target instanceof RiskArea
            ? ($this->user()?->can('update', $target) ?? false)
            : false;
    }

    protected function prepareForValidation(): void
    {
        $riskType = $this->input('risk_type');
        $priority = $this->input('priority_level');
        $isActive = $this->input('is_active');

        $this->merge([
            'risk_type' => is_string($riskType) ? strtoupper(trim($riskType)) : $riskType,
            'priority_level' => is_string($priority) ? strtoupper(trim($priority)) : $priority,
            'is_active' => $isActive === null ? true : filter_var($isActive, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false,
        ]);
    }

    public function rules(): array
    {
        $tenantId = (int) $this->user()->tenant_id;

        return [
            'territorial_unit_id' => [
                'required',
                'integer',
                Rule::exists('territorial_units', 'id')->where(
                    fn ($query) => $query->where('tenant_id', $tenantId),
                ),
            ],
            'name' => ['required', 'string', 'max:200'],
            'risk_type' => ['required', Rule::in(RiskArea::RISK_TYPES)],
            'priority_level' => ['required', Rule::in(RiskArea::PRIORITY_LEVELS)],
            'exposed_population_estimate' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'monitoring_notes' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
