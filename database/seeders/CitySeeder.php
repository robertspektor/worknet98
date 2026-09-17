<?php

namespace Database\Seeders;

use App\Models\City;
use App\World\CityCatalog;
use App\World\HouseholdWriter;
use App\World\Population\LivelihoodFiller;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(CityCatalog $catalog, HouseholdWriter $writer, LivelihoodFiller $livelihoods): void
    {
        foreach ($catalog->cities() as $content) {
            $city = City::query()->updateOrCreate(['slug' => $content['slug']], [
                'name' => $content['name'],
                'locale' => $content['locale'],
            ]);

            $writer->write($city, $catalog->namedHouseholdsOf($city->slug));
            $livelihoods->fillMissing($city);
        }
    }
}
