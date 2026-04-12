<?php

namespace App\Http\Requests\Administration\Property;

use App\Models\Property\Property;
use App\Services\GoogleMapsUrlNormalizer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertyUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Property $property */
        $property = $this->route('property');

        return $this->user()->can('update', $property);
    }

    protected function prepareForValidation(): void
    {
        $merge = [
            'is_furnished' => $this->boolean('is_furnished'),
            'provides_agreement' => $this->boolean('provides_agreement'),
            'suitable_for' => $this->input('suitable_for', []),
            'house_rules' => $this->input('house_rules', []),
            'amenities' => $this->input('amenities', []),
            'included_bills' => $this->input('included_bills', []),
        ];

        if ($this->filled('map_link')) {
            $maps = app(GoogleMapsUrlNormalizer::class)->normalize($this->input('map_link'));
            $merge['map_link'] = $maps['url'];
            if ($maps['latitude'] !== null && $maps['longitude'] !== null) {
                $merge['latitude'] = $maps['latitude'];
                $merge['longitude'] = $maps['longitude'];
            }
        }

        if (! isset($merge['latitude'])) {
            $merge['latitude'] = $this->input('latitude') === '' ? null : $this->input('latitude');
        }
        if (! isset($merge['longitude'])) {
            $merge['longitude'] = $this->input('longitude') === '' ? null : $this->input('longitude');
        }

        $this->merge($merge);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $user = $this->user();

        $listingCategories = $user?->hasRole('Student')
            ? ['shared_room']
            : ['entire_place', 'shared_room'];

        $includedBillsRules = $this->input('bills_included') === 'some'
            ? ['required', 'array', 'min:1']
            : ['nullable', 'array'];

        $rules = [
            'listing_category' => ['required', Rule::in($listingCategories)],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'city_id' => [
                'required',
                'integer',
                Rule::exists('cities', 'id')->where(fn ($q) => $q->where('country_id', $this->input('country_id'))),
            ],
            'area_id' => [
                'required',
                'integer',
                Rule::exists('areas', 'id')->where(fn ($q) => $q->where('city_id', $this->input('city_id'))),
            ],
            'map_link' => ['required', 'string', 'max:2048', 'url'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'distance_university_km' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'distance_transit_km' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'property_type' => ['required', Rule::in(['studio', 'apartment', 'house', 'student_seat'])],
            'bed_type' => [
                Rule::requiredIf(fn () => $this->input('listing_category') === 'shared_room'),
                'nullable',
                Rule::in(['single', 'shared_double']),
            ],
            'bedrooms' => ['required', 'integer', Rule::in([1, 2, 3, 4, 5, 6])],
            'bathrooms' => ['required', 'integer', Rule::in([1, 2, 3])],
            'bathroom_type' => ['required', Rule::in(['private_ensuite', 'shared'])],
            'is_furnished' => ['boolean'],
            'rent_duration' => ['required', Rule::in(['day', 'week', 'month'])],
            'rent_amount' => ['required', 'integer', 'min:1'],
            'bills_included' => ['required', Rule::in(['all', 'some', 'none'])],
            'included_bills' => $includedBillsRules,
            'included_bills.*' => [Rule::in(['wifi', 'water', 'electricity', 'gas'])],
            'min_contract_length' => ['required', Rule::in(['1_month', '3_months', '6_months', '1_year', 'flexible'])],
            'provides_agreement' => ['boolean'],
            'deposit_required' => ['required', Rule::in(['none', '1_month', '5_weeks'])],
            'rent_for' => ['required', Rule::in(['only_boys', 'only_girls', 'couples', 'anyone'])],
            'suitable_for' => ['required', 'array', 'min:1'],
            'suitable_for.*' => [Rule::in(['undergraduates', 'postgraduates', 'couples'])],
            'flatmate_vibe' => [
                Rule::requiredIf(fn () => $this->input('listing_category') === 'shared_room'),
                'nullable',
                Rule::in(['all_male', 'all_female', 'mixed']),
            ],
            'house_rules' => ['array'],
            'house_rules.*' => [Rule::in(['pet_friendly', 'smoking_allowed', 'quiet_house'])],
            'amenities' => ['required', 'array', 'min:1'],
            'amenities.*' => [Rule::in(['wifi', 'washing_machine', 'tumble_dryer', 'dishwasher', 'balcony_garden', 'desk_in_room', 'building_gym', 'bike_storage'])],
        ];

        if ($user?->hasAnyRole(['Developer', 'Super Admin', 'Admin'])) {
            $rules['status'] = ['required', Rule::in([
                Property::STATUS_DRAFT,
                Property::STATUS_PENDING,
                Property::STATUS_PUBLISHED,
                Property::STATUS_REJECTED,
                Property::STATUS_LET_AGREED,
                Property::STATUS_ARCHIVED,
            ])];
        }

        return $rules;
    }
}
