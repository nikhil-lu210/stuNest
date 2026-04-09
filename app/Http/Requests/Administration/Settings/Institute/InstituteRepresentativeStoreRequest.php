<?php

namespace App\Http\Requests\Administration\Settings\Institute;

use App\Models\Institute;
use App\Models\InstituteLocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;

class InstituteRepresentativeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        Validator::extend('unique_userid', function ($attribute, $value, $parameters, $validator) {
            $userIdWithPrefix = 'UID'.$value;
            $existingUserId = DB::table($parameters[0])
                ->where($parameters[1], $userIdWithPrefix)
                ->first();

            return $existingUserId === null;
        });

        /** @var Institute $institute */
        $institute = $this->route('institute');
        $suffix = strtolower($institute->email_code);

        return [
            'institute_location_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($institute) {
                    $exists = InstituteLocation::where('institute_id', $institute->id)
                        ->whereKey($value)
                        ->exists();
                    if (! $exists) {
                        $fail('The selected branch is not part of this institute.');
                    }
                },
            ],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'userid' => [
                'required',
                'string',
                'unique_userid:users,userid',
            ],
            'first_name' => ['required', 'string'],
            'middle_name' => ['nullable', 'string'],
            'last_name' => ['required', 'string'],
            'email' => [
                'required',
                'email',
                'unique:users,email',
                function ($attribute, $value, $fail) use ($suffix) {
                    $email = strtolower((string) $value);
                    if ($suffix !== '' && ! str_ends_with($email, $suffix)) {
                        $fail('The email must end with the institute email code '.$suffix.'.');
                    }
                },
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'userid.unique_userid' => 'The User ID already exists in database. It should be Unique.',
            'avatar.mimes' => 'The avatar must be a JPEG, JPG or PNG image file.',
            'avatar.max' => 'The avatar size should not more then 2MB.',
        ];
    }
}
