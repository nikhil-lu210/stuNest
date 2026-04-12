<?php

namespace App\Http\Requests\Administration\Settings\User;

use App\Support\SystemRoles;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\Permission\Models\Role;

class UserStoreRequest extends FormRequest
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
        Validator::extend('unique_userid', function ($attribute, $value, $parameters, $validator) {
            $userIdWithPrefix = 'UID' . $value;
            $existingUserId = DB::table($parameters[0])
                ->where($parameters[1], $userIdWithPrefix)
                ->first();

            return $existingUserId === null;
        });
        
        return [
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'userid' => [
                'required',
                'string',
                'unique_userid:users,userid', // Custom validation rule
            ],
            'first_name' => ['required', 'string'],
            'middle_name' => ['nullable', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where(function ($query) {
                    $query->where('guard_name', SystemRoles::WEB_GUARD);
                }),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $role = Role::query()->find($value);
                    if ($role && SystemRoles::isDeveloperRole($role) && ! SystemRoles::viewerIsDeveloper($this->user())) {
                        $fail(__('Invalid role.'));
                    }
                },
            ],
        ];
    }

    public function messages()
    {
        return [
            'userid.unique_userid' => 'The User ID already exists in database. It should be Unique.',
            'avatar.mimes' => 'The avatar must be a JPEG, JPG or PNG image file.',
            'avatar.max' => 'The avatar size should not more then 2MB.',
        ];
    }
}
