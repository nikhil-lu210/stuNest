<?php

namespace Database\Seeders\Demo;

use App\Models\Area;
use App\Models\Country;
use App\Models\Property\Property;
use App\Models\User;
use App\Services\GoogleMapsUrlNormalizer;
use Illuminate\Database\Seeder;

/**
 * Fifty published rental listings in Cyprus (realistic Google Maps links + coordinates),
 * attributed to administration users, landlord, student, and agent demo accounts.
 * Attaches six placeholder images per property (remote URLs).
 */
class CyprusRentPropertiesSeeder extends Seeder
{
    private const PROPERTY_COUNT = 12;

    /** @var list<string> */
    private const DEMO_GALLERY_URLS = [
        'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&w=1200&q=80',
        'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&w=1200&q=80',
        'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&w=1200&q=80',
        'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&w=1200&q=80',
        'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&w=1200&q=80',
        'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&w=1200&q=80',
    ];

    public function run(): void
    {
        $cyprus = Country::query()->where('iso_code', 'CY')->first();
        if (! $cyprus) {
            $this->command?->warn('Cyprus country missing. Run CyprusGeographySeeder first.');

            return;
        }

        $areas = Area::query()
            ->whereHas('city', fn ($q) => $q->where('country_id', $cyprus->id))
            ->with('city')
            ->orderBy('id')
            ->get();

        if ($areas->isEmpty()) {
            $this->command?->warn('No Cyprus areas found. Run CyprusGeographySeeder first.');

            return;
        }

        $developer = User::withoutGlobalScopes()->where('email', 'developer@mail.com')->first();
        $superAdmin = User::withoutGlobalScopes()->where('email', 'superadmin@mail.com')->first();
        $landlord = User::withoutGlobalScopes()->where('email', 'demo.landlord@stunest.test')->first();
        $student = User::withoutGlobalScopes()->where('email', 'demo.student@ucy.ac.cy')->first();
        $agent = User::withoutGlobalScopes()->where('email', 'demo.agent@stunest.test')->first();

        if (! $developer || ! $superAdmin || ! $landlord || ! $student || ! $agent) {
            $this->command?->warn('Missing demo users. Run UsersTableSeeder and CyprusPortalDemoSeeder first.');

            return;
        }

        $ownerKinds = $this->shuffledOwnerKinds();
        $faker = fake();
        $normalizer = app(GoogleMapsUrlNormalizer::class);

        foreach (range(0, self::PROPERTY_COUNT - 1) as $index) {
            $kind = $ownerKinds[$index];
            $owner = match ($kind) {
                'admin' => $index % 2 === 0 ? $developer : $superAdmin,
                'landlord' => $landlord,
                'student' => $student,
                'agent' => $agent,
            };

            $area = $areas[$index % $areas->count()];
            [$lat, $lng] = $this->jitteredCoordinatesFor($area);

            $rawMapUrl = sprintf('https://www.google.com/maps?q=%.6f,%.6f', $lat, $lng);
            $normalized = $normalizer->normalize($rawMapUrl);
            $mapLink = $normalized['url'];
            $latitude = $normalized['latitude'] ?? sprintf('%.6f', $lat);
            $longitude = $normalized['longitude'] ?? sprintf('%.6f', $lng);

            $isStudentOwner = $kind === 'student';
            $listingCategory = $isStudentOwner
                ? 'shared_room'
                : $faker->randomElement(['entire_place', 'shared_room']);

            $bedrooms = $faker->numberBetween(1, 5);
            $billsIncluded = $faker->randomElement(['all', 'some', 'none']);
            $includedBills = $billsIncluded === 'some'
                ? $faker->randomElements(['wifi', 'water', 'electricity', 'gas'], $faker->numberBetween(1, 3))
                : [];

            $minContractLength = $faker->randomElement(['1_month', '3_months', '6_months', '1_year', 'flexible']);

            $attributes = [
                'user_id' => $owner->id,
                'country_id' => $cyprus->id,
                'city_id' => $area->city_id,
                'area_id' => $area->id,
                'map_link' => $mapLink,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'distance_university_km' => $faker->randomFloat(2, 0.5, 12),
                'distance_transit_km' => $faker->randomFloat(2, 0.1, 4),
                'listing_category' => $listingCategory,
                'property_type' => $faker->randomElement(['studio', 'apartment', 'house', 'student_seat']),
                'bedrooms' => $bedrooms,
                'bathrooms' => $faker->numberBetween(1, 3),
                'bathroom_type' => $faker->randomElement(['private_ensuite', 'shared']),
                'bed_type' => $listingCategory === 'shared_room'
                    ? $faker->randomElement(['single', 'shared_double'])
                    : null,
                'is_furnished' => $faker->boolean(85),
                'rent_duration' => $faker->randomElement(['week', 'month']),
                'rent_amount' => $faker->numberBetween(280, 980),
                'bills_included' => $billsIncluded,
                'included_bills' => $includedBills,
                'min_contract_length' => $minContractLength,
                'min_contract_weeks' => $this->weeksForContractLength($minContractLength),
                'provides_agreement' => $faker->boolean(70),
                'deposit_required' => $faker->randomElement(['none', '1_month', '5_weeks']),
                'rent_for' => $faker->randomElement(['only_boys', 'only_girls', 'couples', 'anyone']),
                'suitable_for' => $faker->randomElements(['undergraduates', 'postgraduates', 'couples'], $faker->numberBetween(1, 3)),
                'flatmate_vibe' => $listingCategory === 'shared_room'
                    ? $faker->randomElement(['all_male', 'all_female', 'mixed'])
                    : null,
                'house_rules' => $faker->randomElements(
                    ['pet_friendly', 'smoking_allowed', 'quiet_house'],
                    $faker->numberBetween(0, 2)
                ),
                'amenities' => $faker->randomElements(
                    ['wifi', 'washing_machine', 'tumble_dryer', 'dishwasher', 'balcony_garden', 'desk_in_room', 'building_gym', 'bike_storage'],
                    $faker->numberBetween(2, 5)
                ),
                'capacity' => max(1, $bedrooms),
                'available_beds' => max(1, $bedrooms),
                'available_from' => now()->addDays($faker->numberBetween(0, 60))->toDateString(),
                'status' => Property::STATUS_PUBLISHED,
            ];

            $property = Property::query()->create($attributes);

            foreach (range(0, 5) as $slot) {
                $url = self::DEMO_GALLERY_URLS[($index + $slot) % count(self::DEMO_GALLERY_URLS)];
                $property->addMediaFromUrl($url)
                    ->toMediaCollection('property_gallery');
            }
        }
    }

    /**
     * @return list<string>
     */
    private function shuffledOwnerKinds(): array
    {
        $kinds = array_merge(
            array_fill(0, 13, 'admin'),
            array_fill(0, 12, 'landlord'),
            array_fill(0, 12, 'student'),
            array_fill(0, 13, 'agent'),
        );
        shuffle($kinds);

        return $kinds;
    }

    private function weeksForContractLength(string $length): int
    {
        return match ($length) {
            '1_month' => 4,
            '3_months' => 13,
            '6_months' => 26,
            '1_year' => 52,
            'flexible' => 1,
            default => 13,
        };
    }

    /**
     * Approximate pin per city/district in Cyprus (WGS84). Small jitter avoids identical pins.
     *
     * @return array{0: float, 1: float}
     */
    private function jitteredCoordinatesFor(Area $area): array
    {
        $city = $area->city;
        $key = $city->name.'|'.$area->name;

        $points = [
            'Nicosia|Strovolos' => [35.1338, 33.3411],
            'Nicosia|Engomi' => [35.1511, 33.3203],
            'Nicosia|Aglantzia' => [35.1458, 33.3126],
            'Nicosia|Lakatamia' => [35.1031, 33.3147],
            'Nicosia|Old Town' => [35.1728, 33.3647],
            'Limassol|Germasogeia' => [34.7019, 33.0911],
            'Limassol|Agios Athanasios' => [34.7097, 33.0292],
            'Limassol|Kato Polemidia' => [34.6939, 33.0250],
            'Limassol|Mesa Geitonia' => [34.6986, 33.0444],
            'Limassol|Agios Tychonas' => [34.7261, 33.1428],
            'Larnaca|Skala' => [34.9156, 33.6389],
            'Larnaca|Aradippou' => [35.0194, 33.5911],
            'Larnaca|Dromolaxia' => [34.8758, 33.5889],
            'Larnaca|Livadia' => [34.9556, 33.6250],
            'Larnaca|Meneou' => [34.8833, 33.6000],
            'Paphos|Kato Paphos' => [34.7581, 32.4097],
            'Paphos|Chloraka' => [34.7964, 32.4250],
            'Paphos|Tala' => [34.8381, 32.4322],
            'Paphos|Peyia' => [34.8861, 32.3817],
            'Paphos|Geroskipou' => [34.7597, 32.4458],
            'Famagusta|Ayia Napa' => [34.9861, 34.0014],
            'Famagusta|Paralimni' => [35.0394, 33.9814],
            'Famagusta|Protaras' => [35.0131, 34.0561],
            'Famagusta|Deryneia' => [35.0647, 33.9636],
            'Famagusta|Sotira' => [35.0081, 33.9419],
            'Kyrenia|Kyrenia Centre' => [35.3364, 33.3173],
            'Kyrenia|Alsancak' => [35.3542, 33.2386],
            'Kyrenia|Karaoğlanoğlu' => [35.3389, 33.2389],
            'Kyrenia|Çatalköy' => [35.3306, 33.4750],
            'Kyrenia|Bellapais' => [35.3050, 33.3583],
        ];

        $cityFallback = [
            'Nicosia' => [35.1753, 33.3642],
            'Limassol' => [34.7071, 33.0226],
            'Larnaca' => [34.9089, 33.6361],
            'Paphos' => [34.7720, 32.4297],
            'Famagusta' => [35.1250, 33.9500],
            'Kyrenia' => [35.3364, 33.3173],
        ];

        [$lat, $lng] = $points[$key] ?? ($cityFallback[$city->name] ?? [35.1753, 33.3642]);

        return [
            round($lat + (mt_rand(-40, 40) / 10000), 6),
            round($lng + (mt_rand(-40, 40) / 10000), 6),
        ];
    }
}
