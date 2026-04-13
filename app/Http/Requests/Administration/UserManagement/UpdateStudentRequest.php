<?php

namespace App\Http\Requests\Administration\UserManagement;

use App\Models\Institute;
use App\Models\User;
use App\Support\StudentCountryList;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
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

        return $user->hasRole('Student');
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

        $uid = $this->input('university_id');
        if ($uid === null || $uid === '') {
            return;
        }

        $institute = Institute::query()->find((int) $uid);
        if (! $institute) {
            return;
        }

        $prefix = strtolower(trim((string) $this->input('email_prefix')));
        $domain = Institute::normalizeEmailCode($institute->email_code);
        $this->merge([
            'email' => $prefix.$domain,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $user = $this->route('user');
        assert($user instanceof User);

        $universityId = $this->input('university_id');

        return [
            'student_name' => ['required', 'string', 'max:255', 'regex:/^\S+(?:\s+\S+)+$/'],
            'country_code' => ['required', 'string', 'size:2', Rule::in(StudentCountryList::codes())],
            'university_id' => ['required', 'integer', 'exists:institutes,id'],
            'institute_location_id' => [
                'required',
                'integer',
                Rule::exists('institute_locations', 'id')->where(function ($query) use ($universityId) {
                    return $query->where('institute_id', $universityId);
                }),
            ],
            'email_prefix' => ['required', 'string', 'max:120', 'regex:/^[a-zA-Z0-9._-]+$/'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'account_status' => ['required', 'string', Rule::in([
                User::ACCOUNT_STATUS_ACTIVE,
                User::ACCOUNT_STATUS_PENDING,
                User::ACCOUNT_STATUS_REJECTED,
                User::ACCOUNT_STATUS_UNVERIFIED,
            ])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'student_name.regex' => __('Enter the student’s first and last name (at least two words).'),
        ];
    }
}
