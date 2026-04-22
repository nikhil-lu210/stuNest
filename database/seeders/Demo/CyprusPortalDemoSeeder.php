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
 * Demo institute (Cyprus), campus location, institute rep, student, landlord, and agent — password: 12345678.
 */
class CyprusPortalDemoSeeder extends Seeder
{
    public const DEMO_PASSWORD = '12345678';

    public function run(): void
    {
        $cyprus = Country::query()->where('iso_code', 'CY')->first();
        $nicosia = $cyprus
            ? City::query()->where('country_id', $cyprus->id)->where('name', 'Nicosia')->first()
            : null;
        $aglantzia = $nicosia
            ? Area::query()->where('city_id', $nicosia->id)->where('name', 'Aglantzia')->first()
            : null;

        if (! $cyprus || ! $nicosia || ! $aglantzia) {
            $this->command?->warn('Cyprus geography missing. Run CyprusGeographySeeder before CyprusPortalDemoSeeder.');

            return;
        }

        DB::transaction(function () use ($cyprus, $nicosia, $aglantzia) {
            $institute = Institute::query()->firstOrCreate(
                ['slug' => 'university-of-cyprus'],
                [
                    'name' => 'University of Cyprus',
                    'email_code' => '@ucy.ac.cy',
                ],
            );

            $location = InstituteLocation::query()->firstOrCreate(
                [
                    'institute_id' => $institute->id,
                    'name' => 'Main Campus — Nicosia',
                ],
                [
                    'address_line_1' => '1 University Avenue',
                    'postcode' => '2109',
                    'country_id' => $cyprus->id,
                    'city_id' => $nicosia->id,
                    'area_id' => $aglantzia->id,
                    'is_primary' => true,
                    'sort_order' => 0,
                ],
            );

            $password = Hash::make(self::DEMO_PASSWORD);

            $repUser = User::withoutGlobalScopes()->firstOrCreate(
                ['email' => 'institute.rep@ucy.ac.cy'],
                [
                    'userid' => strtoupper(Str::random(8)),
                    'first_name' => 'Elena',
                    'last_name' => 'Georgiou',
                    'password' => $password,
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                    'account_status' => User::ACCOUNT_STATUS_ACTIVE,
                ],
            );
            $repUser->forceFill(['password' => $password])->saveQuietly();
            if (! $repUser->hasRole('Institute Representative')) {
                $repUser->assignRole(Role::findByName('Institute Representative', 'institute'));
            }

            InstituteRepresentative::query()->firstOrCreate(
                [
                    'institute_id' => $institute->id,
                    'institute_location_id' => $location->id,
                    'user_id' => $repUser->id,
                ],
            );

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
                    'institution_id' => $institute->id,
                    'institute_location_id' => $location->id,
                    'country_code' => 'CY',
                ],
            );
            $student->forceFill(['password' => $password])->saveQuietly();
            if (! $student->hasRole('Student')) {
                $student->assignRole(Role::findByName('Student', 'student'));
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
}
