<?php

namespace App\Http\Requests\Administration\Settings\Institute;

use App\Models\Area;
use App\Models\City;
use App\Models\InstituteLocation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class InstituteUpdateRequest extends FormRequest
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
        $institute = $this->route('institute');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email_code' => [
                'required',
                'string',
                'max:125',
                'regex:/^@?[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                Rule::unique('institutes', 'email_code')->ignore($institute->id),
            ],
            'locations' => ['required', 'array', 'min:1'],
            'locations.*.id' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) use ($institute) {
                    if ($value === null || $value === '') {
                        return;
                    }
                    $exists = InstituteLocation::where('institute_id', $institute->id)
                        ->whereKey($value)
                        ->exists();
                    if (! $exists) {
                        $fail('One of the locations does not belong to this institute.');
                    }
                },
            ],
            'locations.*.name' => ['required', 'string', 'max:255'],
            'locations.*.address_line_1' => ['nullable', 'string', 'max:255'],
            'locations.*.postcode' => ['nullable', 'string', 'max:32'],
            'locations.*.country_id' => ['required', 'integer', 'exists:countries,id'],
            'locations.*.city_id' => ['required', 'integer', 'exists:cities,id'],
            'locations.*.area_id' => ['required', 'integer', 'exists:areas,id'],
            'locations.*.is_primary' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $locations = $this->input('locations', []);
            if (! is_array($locations)) {
                return;
            }
            foreach ($locations as $index => $row) {
                $countryId = $row['country_id'] ?? null;
                $cityId = $row['city_id'] ?? null;
                $areaId = $row['area_id'] ?? null;
                if (! $countryId || ! $cityId || ! $areaId) {
                    continue;
                }
                $city = City::query()->find($cityId);
                if (! $city || (int) $city->country_id !== (int) $countryId) {
                    $validator->errors()->add(
                        "locations.{$index}.city_id",
                        __('The selected city does not belong to the selected country.')
                    );
                }
                $area = Area::query()->find($areaId);
                if (! $area || (int) $area->city_id !== (int) $cityId) {
                    $validator->errors()->add(
                        "locations.{$index}.area_id",
                        __('The selected area does not belong to the selected city.')
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'email_code.regex' => 'The email code must look like @university.ac.uk (domain suffix only).',
        ];
    }

    protected function prepareForValidation(): void
    {
        $emailCode = $this->input('email_code');
        if (is_string($emailCode) && $emailCode !== '' && ! str_starts_with(trim($emailCode), '@')) {
            $this->merge([
                'email_code' => '@'.ltrim($emailCode, '@'),
            ]);
        }

        $locations = $this->input('locations', []);
        if (is_array($locations)) {
            $primaryIndex = $this->input('primary_location_index');
            if ($primaryIndex !== null && $primaryIndex !== '') {
                $primaryIndex = (string) $primaryIndex;
            } else {
                $primaryIndex = null;
                foreach ($locations as $index => $row) {
                    if (! empty($row['is_primary'])) {
                        $primaryIndex = (string) $index;
                        break;
                    }
                }
            }
            if ($primaryIndex === null && count($locations) > 0) {
                $primaryIndex = (string) array_key_first($locations);
            }
            foreach ($locations as $index => $row) {
                $locations[$index]['is_primary'] = ($primaryIndex !== null && (string) $index === $primaryIndex);
            }
            $this->merge(['locations' => $locations]);
        }
    }
}
