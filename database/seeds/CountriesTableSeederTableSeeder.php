<?php

use App\Country;
use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class CountriesTableSeederTableSeeder extends Seeder
{
    public function run()
    {
        $countries = \App\Libraries\CoutriesLib::getCountryWithContinent();

        foreach ($countries as $countryKey => $country) {
            DB::table('countries')->insert(array(
                    'country_code'   => $country['country_code'],
                    'country_name'   => $country['country_name'],
                    'continent_code' => $country['continent_code'],
                    'continent_name' => $country['continent_name'],
                    'created_at'     => new DateTime,
                    'updated_at'     => new DateTime
                )
            );
//
        }
    }
}
