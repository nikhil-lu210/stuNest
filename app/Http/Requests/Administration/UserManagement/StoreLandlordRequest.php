<?php

namespace App\Http\Requests\Administration\UserManagement;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLandlordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('User Create') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'billing_address' => ['nullable', 'string', 'max:2000'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'account_status' => ['required', 'string', Rule::in([
                User::ACCOUNT_STATUS_ACTIVE,
                User::ACCOUNT_STATUS_PENDING,
                User::ACCOUNT_STATUS_REJECTED,
            ])],
        ];
    }
}
