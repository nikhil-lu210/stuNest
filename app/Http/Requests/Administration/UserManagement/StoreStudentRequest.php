<?php

namespace App\Http\Requests\Administration\UserManagement;

use App\Models\Institute;
use App\Support\StudentCountryList;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('User Create') ?? false;
    }

    protected function prepareForValidation(): void
    {
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
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
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
