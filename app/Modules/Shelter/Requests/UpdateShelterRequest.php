<?php

namespace App\Modules\Shelter\Requests;

use App\Modules\Shelter\Models\Shelter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShelterRequest extends FormRequest
{
    public function authorize(): bool
    {
        $target = $this->route('shelter');

        return $target instanceof Shelter
            ? ($this->user()?->can('update', $target) ?? false)
            : false;
    }

    protected function prepareForValidation(): void
    {
        $shelterType = $this->input('shelter_type');

        $this->merge([
            'shelter_type' => is_string($shelterType) ? strtoupper(trim($shelterType)) : $shelterType,
            'kitchen_available' => $this->toBool($this->input('kitchen_available')),
            'water_supply_available' => $this->toBool($this->input('water_supply_available')),
            'energy_supply_available' => $this->toBool($this->input('energy_supply_available')),
            'is_active' => $this->toBool($this->input('is_active'), true),
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
            'shelter_type' => ['required', Rule::in(Shelter::SHELTER_TYPES)],
            'address' => ['required', 'string'],
            'manager_name' => ['nullable', 'string', 'max:150'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'max_people_capacity' => ['required', 'integer', 'min:0'],
            'accessibility_features' => ['nullable', 'string'],
            'kitchen_available' => ['required', 'boolean'],
            'water_supply_available' => ['required', 'boolean'],
            'energy_supply_available' => ['required', 'boolean'],
            'sanitary_structure_description' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    private function toBool(mixed $value, bool $default = false): bool
    {
        if ($value === null) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false;
    }
}
