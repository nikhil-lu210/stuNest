<?php

namespace App\Services;

use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class GeographyImportService
{
    /**
     * @return array{countries: int, cities: int, areas: int}
     */
    public function importFromArray(array $payload): array
    {
        if (! isset($payload['countries']) || ! is_array($payload['countries'])) {
            throw new InvalidArgumentException('JSON must contain a "countries" array.');
        }

        $counts = ['countries' => 0, 'cities' => 0, 'areas' => 0];

        DB::transaction(function () use ($payload, &$counts) {
            foreach ($payload['countries'] as $countryRow) {
                if (empty($countryRow['iso_code']) || empty($countryRow['name'])) {
                    throw new InvalidArgumentException('Each country requires iso_code and name.');
                }

                $iso = strtoupper(substr((string) $countryRow['iso_code'], 0, 2));
                $country = Country::updateOrCreate(
                    ['iso_code' => $iso],
                    [
                        'name' => (string) $countryRow['name'],
                        'is_active' => array_key_exists('is_active', $countryRow)
                            ? (bool) $countryRow['is_active']
                            : true,
                    ]
                );
                $counts['countries']++;

                $cities = $countryRow['cities'] ?? [];
                if (! is_array($cities)) {
                    throw new InvalidArgumentException('Country '.$iso.' cities must be an array.');
                }

                $cityOrder = 0;
                foreach ($cities as $cityRow) {
                    $cityOrder++;
                    $cityName = null;
                    $cityActive = true;
                    $areasData = [];

                    if (is_string($cityRow)) {
                        $cityName = $cityRow;
                        $areasData = [];
                    } elseif (is_array($cityRow)) {
                        $cityName = $cityRow['name'] ?? null;
                        $cityActive = array_key_exists('is_active', $cityRow)
                            ? (bool) $cityRow['is_active']
                            : true;
                        $areasData = $cityRow['areas'] ?? [];
                        $cityOrder = isset($cityRow['sort_order']) ? (int) $cityRow['sort_order'] : $cityOrder;
                    }

                    if ($cityName === null || $cityName === '') {
                        throw new InvalidArgumentException('Each city must have a name.');
                    }

                    $city = City::updateOrCreate(
                        [
                            'country_id' => $country->id,
                            'name' => $cityName,
                        ],
                        [
                            'is_active' => $cityActive,
                            'sort_order' => $cityOrder,
                        ]
                    );
                    $counts['cities']++;

                    if (! is_array($areasData)) {
                        throw new InvalidArgumentException('Areas for city '.$cityName.' must be an array.');
                    }

                    $areaOrder = 0;
                    foreach ($areasData as $areaRow) {
                        $areaOrder++;
                        $areaName = null;
                        $areaActive = true;

                        if (is_string($areaRow)) {
                            $areaName = $areaRow;
                        } elseif (is_array($areaRow)) {
                            $areaName = $areaRow['name'] ?? null;
                            $areaActive = array_key_exists('is_active', $areaRow)
                                ? (bool) $areaRow['is_active']
                                : true;
                            $areaOrder = isset($areaRow['sort_order']) ? (int) $areaRow['sort_order'] : $areaOrder;
                        }

                        if ($areaName === null || $areaName === '') {
                            continue;
                        }

                        Area::updateOrCreate(
                            [
                                'city_id' => $city->id,
                                'name' => $areaName,
                            ],
                            [
                                'is_active' => $areaActive,
                                'sort_order' => $areaOrder,
                            ]
                        );
                        $counts['areas']++;
                    }
                }
            }
        });

        return $counts;
    }
}
