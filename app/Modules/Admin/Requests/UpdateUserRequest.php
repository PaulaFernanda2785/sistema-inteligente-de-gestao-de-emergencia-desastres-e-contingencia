<?php

namespace App\Modules\Admin\Requests;

use App\Modules\Admin\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $authUser = $this->user();
        if (!$authUser instanceof User) {
            return false;
        }

        return $authUser->hasPermission('users.update');
    }

    public function rules(): array
    {
        $tenantId = (int) $this->user()->tenant_id;
        $targetUserId = (int) $this->route('user');

        return [
            'name' => ['required', 'string', 'max:200'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')
                    ->ignore($targetUserId)
                    ->where(fn ($query) => $query->where('tenant_id', $tenantId)->whereNull('deleted_at')),
            ],
            'cpf_hash' => ['nullable', 'string', 'max:255'],
            'organization_id' => [
                'required',
                'integer',
                Rule::exists('organizations', 'id')->where(
                    fn ($query) => $query->where('tenant_id', $tenantId)->where('is_active', true),
                ),
            ],
            'unit_id' => [
                'nullable',
                'integer',
                Rule::exists('organizational_units', 'id')->where(
                    fn ($query) => $query->where('tenant_id', $tenantId)->where('is_active', true),
                ),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'position_name' => ['nullable', 'string', 'max:150'],
            'status' => ['required', Rule::in(['ATIVO', 'INATIVO'])],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
            'roles' => ['nullable', 'array'],
            'roles.*' => [
                'integer',
                Rule::exists('roles', 'id')->where(
                    fn ($query) => $query->where('tenant_id', $tenantId)->orWhereNull('tenant_id'),
                ),
            ],
        ];
    }
}
