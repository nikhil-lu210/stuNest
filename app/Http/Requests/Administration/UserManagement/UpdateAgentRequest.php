<?php

namespace App\Http\Requests\Administration\UserManagement;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAgentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->route('user');
        if (! $user instanceof User) {
            return false;
        }

        $auth = $this->user();
        if (! $auth?->can('User Update')) {
            return false;
        }

        if (! $auth->can('update', $user)) {
            return false;
        }

        return $user->hasRole('Agent');
    }

    protected function prepareForValidation(): void
    {
        $pw = $this->input('password');
        if ($pw === '' || $pw === null) {
            $this->merge([
                'password' => null,
                'password_confirmation' => null,
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $user = $this->route('user');
        assert($user instanceof User);

        return [
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['required', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'agency_name' => ['required', 'string', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:255'],
            'office_address' => ['nullable', 'string', 'max:2000'],
            'account_status' => ['required', 'string', Rule::in([
                User::ACCOUNT_STATUS_ACTIVE,
                User::ACCOUNT_STATUS_PENDING,
                User::ACCOUNT_STATUS_REJECTED,
            ])],
        ];
    }
}
