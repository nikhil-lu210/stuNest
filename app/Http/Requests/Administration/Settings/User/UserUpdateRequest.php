<?php

namespace App\Http\Requests\Administration\Settings\User;

use App\Support\SystemRoles;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\Permission\Models\Role;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules()
    {
        $id = $this->route('user')->id;
        return [
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'first_name' => ['required', 'string'],
            'middle_name' => ['nullable', 'string'],
            'last_name' => ['required', 'string'],
            'email' => [
                'required', 
                'email',
                Rule::unique('users')->ignore($id)
            ],
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where(function ($query) {
                    $query->where('guard_name', SystemRoles::WEB_GUARD);
                }),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $role = Role::query()->find($value);
                    $subject = $this->route('user');
                    if (! $role || ! $subject) {
                        return;
                    }
                    if (SystemRoles::isDeveloperRole($role) && ! SystemRoles::viewerIsDeveloper($this->user())) {
                        $fail(__('Invalid role.'));
                    }
                    if ($subject->developer_anchor && ! SystemRoles::isDeveloperRole($role)) {
                        $fail(__('This user\'s role cannot be changed.'));
                    }
                    if ($subject->super_admin_anchor && ! SystemRoles::isSuperAdminRole($role)) {
                        $fail(__('This user\'s role cannot be changed.'));
                    }
                },
            ],
        ];
    }

    public function messages()
    {
        return [
            'avatar.mimes' => 'The avatar must be a JPEG, JPG or PNG image file.',
            'avatar.max' => 'The avatar size should not more then 2MB.',
        ];
    }
}
