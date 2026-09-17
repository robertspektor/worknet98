<?php

namespace Database\Seeders;

use App\Models\City;
use App\World\CityCatalog;
use App\World\HouseholdWriter;
use App\World\Population\NamePool;
use App\World\Population\PopulationPlanner;
use Illuminate\Database\Seeder;

class PopulationSeeder extends Seeder
{
    public function run(CityCatalog $catalog, PopulationPlanner $planner, HouseholdWriter $writer): void
    {
        foreach ($catalog->cities() as $content) {
            $city = City::query()->where('slug', $content['slug'])->firstOrFail();

            $writer->write($city, $planner->plan(
                citySlug: $city->slug,
                districts: $content['districts'],
                residents: $content['population'],
                pool: NamePool::forLocale($city->locale),
                namedHouseholds: $catalog->namedHouseholdsOf($city->slug),
            ));
        }
    }
}
