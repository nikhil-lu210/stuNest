<?php

namespace Database\Factories\Property;

use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        $country = Country::query()->first();
        $city = $country ? City::query()->where('country_id', $country->id)->first() : null;
        $area = $city ? Area::query()->where('city_id', $city->id)->first() : null;

        return [
            'country_id' => $country?->id,
            'city_id' => $city?->id,
            'area_id' => $area?->id,
            'map_link' => 'https://maps.google.com/?q=51.5,-0.12',
            'latitude' => null,
            'longitude' => null,
            'distance_university_km' => 1.5,
            'distance_transit_km' => 0.4,
            'bed_type' => 'single',
            'listing_category' => 'shared_room',
            'property_type' => 'apartment',
            'bedrooms' => 2,
            'bathrooms' => 1,
            'bathroom_type' => 'shared',
            'is_furnished' => true,
            'rent_duration' => 'week',
            'rent_amount' => 150,
            'bills_included' => 'some',
            'included_bills' => ['wifi', 'water'],
            'min_contract_length' => '1_year',
            'provides_agreement' => true,
            'deposit_required' => '5_weeks',
            'rent_for' => 'anyone',
            'suitable_for' => ['undergraduates'],
            'flatmate_vibe' => 'mixed',
            'house_rules' => [],
            'amenities' => ['wifi'],
            'capacity' => 2,
            'available_beds' => 2,
            'status' => 'draft',
        ];
    }
}
