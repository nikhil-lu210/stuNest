<?php

namespace Database\Seeders\Geography;

use App\Services\GeographyImportService;
use Illuminate\Database\Seeder;

class CyprusGeographySeeder extends Seeder
{
    /**
     * Seed Cyprus with major cities and sample areas (districts).
     */
    public function run(): void
    {
        $payload = [
            'countries' => [
                [
                    'iso_code' => 'CY',
                    'name' => 'Cyprus',
                    'is_active' => true,
                    'cities' => [
                        [
                            'name' => 'Nicosia',
                            'sort_order' => 1,
                            'areas' => [
                                'Strovolos',
                                'Engomi',
                                'Aglantzia',
                                'Lakatamia',
                                'Old Town',
                            ],
                        ],
                        [
                            'name' => 'Limassol',
                            'sort_order' => 2,
                            'areas' => [
                                'Germasogeia',
                                'Agios Athanasios',
                                'Kato Polemidia',
                                'Mesa Geitonia',
                                'Agios Tychonas',
                            ],
                        ],
                        [
                            'name' => 'Larnaca',
                            'sort_order' => 3,
                            'areas' => [
                                'Skala',
                                'Aradippou',
                                'Dromolaxia',
                                'Livadia',
                                'Meneou',
                            ],
                        ],
                        [
                            'name' => 'Paphos',
                            'sort_order' => 4,
                            'areas' => [
                                'Kato Paphos',
                                'Chloraka',
                                'Tala',
                                'Peyia',
                                'Geroskipou',
                            ],
                        ],
                        [
                            'name' => 'Famagusta',
                            'sort_order' => 5,
                            'areas' => [
                                'Ayia Napa',
                                'Paralimni',
                                'Protaras',
                                'Deryneia',
                                'Sotira',
                            ],
                        ],
                        [
                            'name' => 'Kyrenia',
                            'sort_order' => 6,
                            'areas' => [
                                'Kyrenia Centre',
                                'Alsancak',
                                'Karaoğlanoğlu',
                                'Çatalköy',
                                'Bellapais',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        app(GeographyImportService::class)->importFromArray($payload);
    }
}
