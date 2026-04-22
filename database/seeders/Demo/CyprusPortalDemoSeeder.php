<?php

namespace Database\Seeders\Demo;

use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use App\Models\Institute;
use App\Models\InstituteLocation;
use App\Models\InstituteRepresentative;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * Demo Cyprus institutes (10+), each with a campus location and at least one institute representative;
 * plus demo student (UCY), landlord, and agent — password: 12345678.
 */
class CyprusPortalDemoSeeder extends Seeder
{
    public const DEMO_PASSWORD = '12345678';

    public function run(): void
    {
        $cyprus = Country::query()->where('iso_code', 'CY')->first();

        if (! $cyprus) {
            $this->command?->warn('Cyprus geography missing. Run CyprusGeographySeeder before CyprusPortalDemoSeeder.');

            return;
        }

        DB::transaction(function () use ($cyprus) {
            $password = Hash::make(self::DEMO_PASSWORD);
            $repRole = Role::findByName('Institute Representative', 'institute');

            $ucyInstitute = null;
            $ucyLocation = null;

            foreach ($this->instituteDefinitions() as $def) {
                $city = City::query()->where('country_id', $cyprus->id)->where('name', $def['city'])->first();
                $area = $city
                    ? Area::query()->where('city_id', $city->id)->where('name', $def['area'])->first()
                    : null;

                if (! $city || ! $area) {
                    $this->command?->warn("Skipping institute \"{$def['name']}\": city/area {$def['city']} / {$def['area']} not found.");

                    continue;
                }

                $institute = Institute::query()->firstOrCreate(
                    ['slug' => $def['slug']],
                    [
                        'name' => $def['name'],
                        'email_code' => $def['email_code'],
                    ],
                );

                $location = InstituteLocation::query()->firstOrCreate(
                    [
                        'institute_id' => $institute->id,
                        'name' => $def['campus'],
                    ],
                    [
                        'address_line_1' => $def['address_line_1'],
                        'postcode' => $def['postcode'],
                        'country_id' => $cyprus->id,
                        'city_id' => $city->id,
                        'area_id' => $area->id,
                        'is_primary' => true,
                        'sort_order' => 0,
                    ],
                );

                if ($def['slug'] === 'university-of-cyprus') {
                    $ucyInstitute = $institute;
                    $ucyLocation = $location;
                }

                $repUser = User::withoutGlobalScopes()->firstOrCreate(
                    ['email' => $def['rep_email']],
                    [
                        'userid' => strtoupper(Str::random(8)),
                        'first_name' => $def['rep_first_name'],
                        'last_name' => $def['rep_last_name'],
                        'password' => $password,
                        'email_verified_at' => now(),
                        'remember_token' => Str::random(10),
                        'account_status' => User::ACCOUNT_STATUS_ACTIVE,
                    ],
                );
                $repUser->forceFill(['password' => $password])->saveQuietly();
                if (! $repUser->hasRole('Institute Representative')) {
                    $repUser->assignRole($repRole);
                }

                InstituteRepresentative::query()->firstOrCreate(
                    [
                        'institute_id' => $institute->id,
                        'institute_location_id' => $location->id,
                        'user_id' => $repUser->id,
                    ],
                );
            }

            if ($ucyInstitute && $ucyLocation) {
                $student = User::withoutGlobalScopes()->firstOrCreate(
                    ['email' => 'demo.student@ucy.ac.cy'],
                    [
                        'userid' => strtoupper(Str::random(8)),
                        'first_name' => 'Andreas',
                        'last_name' => 'Demetriou',
                        'password' => $password,
                        'email_verified_at' => now(),
                        'remember_token' => Str::random(10),
                        'role' => User::ROLE_STUDENT,
                        'account_status' => User::ACCOUNT_STATUS_ACTIVE,
                        'institution_id' => $ucyInstitute->id,
                        'institute_location_id' => $ucyLocation->id,
                        'country_code' => 'CY',
                    ],
                );
                $student->forceFill([
                    'password' => $password,
                    'institution_id' => $ucyInstitute->id,
                    'institute_location_id' => $ucyLocation->id,
                ])->saveQuietly();
                if (! $student->hasRole('Student')) {
                    $student->assignRole(Role::findByName('Student', 'student'));
                }
            } else {
                $this->command?->warn('University of Cyprus institute/location was not created; demo student was skipped.');
            }

            $landlord = User::withoutGlobalScopes()->firstOrCreate(
                ['email' => 'demo.landlord@stunest.test'],
                [
                    'userid' => strtoupper(Str::random(8)),
                    'first_name' => 'Kyriakos',
                    'last_name' => 'Ioannou',
                    'password' => $password,
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                    'role' => User::ROLE_LANDLORD,
                    'account_status' => User::ACCOUNT_STATUS_ACTIVE,
                    'country_code' => 'CY',
                ],
            );
            $landlord->forceFill(['password' => $password])->saveQuietly();
            if (! $landlord->hasRole('Landlord')) {
                $landlord->assignRole(Role::findByName('Landlord', 'landlord'));
            }

            $agent = User::withoutGlobalScopes()->firstOrCreate(
                ['email' => 'demo.agent@stunest.test'],
                [
                    'userid' => strtoupper(Str::random(8)),
                    'first_name' => 'Maria',
                    'last_name' => 'Charalambous',
                    'password' => $password,
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                    'role' => User::ROLE_AGENT,
                    'account_status' => User::ACCOUNT_STATUS_ACTIVE,
                    'agency_name' => 'StuNest Demo Realty — Limassol',
                    'country_code' => 'CY',
                ],
            );
            $agent->forceFill(['password' => $password])->saveQuietly();
            if (! $agent->hasRole('Agent')) {
                $agent->assignRole(Role::findByName('Agent', 'agent'));
            }
        });
    }

    /**
     * Real Cyprus higher-education institutions; campuses use cities/areas from CyprusGeographySeeder.
     *
     * @return list<array{
     *     name: string,
     *     slug: string,
     *     email_code: string,
     *     city: string,
     *     area: string,
     *     campus: string,
     *     address_line_1: string,
     *     postcode: string,
     *     rep_email: string,
     *     rep_first_name: string,
     *     rep_last_name: string,
     * }>
     */
    private function instituteDefinitions(): array
    {
        return [
            [
                'name' => 'University of Cyprus',
                'slug' => 'university-of-cyprus',
                'email_code' => '@ucy.ac.cy',
                'city' => 'Nicosia',
                'area' => 'Aglantzia',
                'campus' => 'Main Campus — Nicosia',
                'address_line_1' => '1 University Avenue',
                'postcode' => '2109',
                'rep_email' => 'institute.rep@ucy.ac.cy',
                'rep_first_name' => 'Elena',
                'rep_last_name' => 'Georgiou',
            ],
            [
                'name' => 'Cyprus University of Technology',
                'slug' => 'cyprus-university-of-technology',
                'email_code' => '@cut.ac.cy',
                'city' => 'Limassol',
                'area' => 'Agios Athanasios',
                'campus' => 'CUT Campus — Limassol',
                'address_line_1' => '30 Archbishop Kyprianos Street',
                'postcode' => '3036',
                'rep_email' => 'rep.cut@stunest.test',
                'rep_first_name' => 'Christos',
                'rep_last_name' => 'Petrou',
            ],
            [
                'name' => 'Open University of Cyprus',
                'slug' => 'open-university-of-cyprus',
                'email_code' => '@ouc.ac.cy',
                'city' => 'Larnaca',
                'area' => 'Livadia',
                'campus' => 'Administrative Hub — Larnaca',
                'address_line_1' => '18 Plateia Grigori Afxentiou',
                'postcode' => '7060',
                'rep_email' => 'rep.ouc@stunest.test',
                'rep_first_name' => 'Despina',
                'rep_last_name' => 'Louka',
            ],
            [
                'name' => 'European University Cyprus',
                'slug' => 'european-university-cyprus',
                'email_code' => '@euc.ac.cy',
                'city' => 'Nicosia',
                'area' => 'Engomi',
                'campus' => 'EUC Campus — Engomi',
                'address_line_1' => '6 Diogenis Street',
                'postcode' => '2404',
                'rep_email' => 'rep.euc@stunest.test',
                'rep_first_name' => 'Michalis',
                'rep_last_name' => 'Andreou',
            ],
            [
                'name' => 'University of Nicosia',
                'slug' => 'university-of-nicosia',
                'email_code' => '@unic.ac.cy',
                'city' => 'Nicosia',
                'area' => 'Strovolos',
                'campus' => 'UNIC Main Campus',
                'address_line_1' => '46 Makedonitissas Avenue',
                'postcode' => '2417',
                'rep_email' => 'rep.unic@stunest.test',
                'rep_first_name' => 'Stavroula',
                'rep_last_name' => 'Panayi',
            ],
            [
                'name' => 'Neapolis University Pafos',
                'slug' => 'neapolis-university-pafos',
                'email_code' => '@nup.ac.cy',
                'city' => 'Paphos',
                'area' => 'Kato Paphos',
                'campus' => 'Neapolis Campus — Paphos',
                'address_line_1' => '2 Danais Avenue',
                'postcode' => '8041',
                'rep_email' => 'rep.nup@stunest.test',
                'rep_first_name' => 'Panikos',
                'rep_last_name' => 'Michael',
            ],
            [
                'name' => 'Frederick University',
                'slug' => 'frederick-university',
                'email_code' => '@frederick.ac.cy',
                'city' => 'Limassol',
                'area' => 'Mesa Geitonia',
                'campus' => 'Frederick Limassol Campus',
                'address_line_1' => '7 Y. Frederickou Street',
                'postcode' => '3080',
                'rep_email' => 'rep.frederick@stunest.test',
                'rep_first_name' => 'Niki',
                'rep_last_name' => 'Constantinou',
            ],
            [
                'name' => 'Cyprus International Institute of Management',
                'slug' => 'ciim-business-school',
                'email_code' => '@ciim.ac.cy',
                'city' => 'Nicosia',
                'area' => 'Old Town',
                'campus' => 'CIIM — Nicosia',
                'address_line_1' => '21 Akademias Avenue',
                'postcode' => '2107',
                'rep_email' => 'rep.ciim@stunest.test',
                'rep_first_name' => 'George',
                'rep_last_name' => 'Hadjikyriakou',
            ],
            [
                'name' => 'Casa College',
                'slug' => 'casa-college',
                'email_code' => '@casacollege.ac.cy',
                'city' => 'Larnaca',
                'area' => 'Skala',
                'campus' => 'Casa College — Larnaca',
                'address_line_1' => '11 Kimonos Street',
                'postcode' => '6035',
                'rep_email' => 'rep.casa@stunest.test',
                'rep_first_name' => 'Anna',
                'rep_last_name' => 'Savva',
            ],
            [
                'name' => 'Global College',
                'slug' => 'global-college',
                'email_code' => '@globalcollege.ac.cy',
                'city' => 'Nicosia',
                'area' => 'Lakatamia',
                'campus' => 'Global College — Lakatamia',
                'address_line_1' => '56 Archiepiskopou Makariou III Avenue',
                'postcode' => '2320',
                'rep_email' => 'rep.global@stunest.test',
                'rep_first_name' => 'Katerina',
                'rep_last_name' => 'Ioannidou',
            ],
            [
                'name' => 'Mesoyios College',
                'slug' => 'mesoyios-college',
                'email_code' => '@mesoyios.ac.cy',
                'city' => 'Limassol',
                'area' => 'Germasogeia',
                'campus' => 'Mesoyios — Germasogeia',
                'address_line_1' => '9 Amathountos Avenue',
                'postcode' => '4044',
                'rep_email' => 'rep.mesoyios@stunest.test',
                'rep_first_name' => 'Petros',
                'rep_last_name' => 'Neophytou',
            ],
            [
                'name' => 'CDA College',
                'slug' => 'cda-college',
                'email_code' => '@cda.ac.cy',
                'city' => 'Paphos',
                'area' => 'Chloraka',
                'campus' => 'CDA College — Chloraka',
                'address_line_1' => '3 Chlorakas Avenue',
                'postcode' => '8220',
                'rep_email' => 'rep.cda@stunest.test',
                'rep_first_name' => 'Sophia',
                'rep_last_name' => 'Demetriades',
            ],
        ];
    }
}
