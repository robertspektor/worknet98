<?php

namespace App\CivilRegistry;

use App\Models\City;
use App\World\Population\NamePool;
use Random\Randomizer;

class PetPicker
{
    public function pick(City $city, Randomizer $randomizer): string
    {
        $pool = NamePool::forLocale($city->locale);
        $name = $pool->petNames[$randomizer->getInt(0, count($pool->petNames) - 1)];
        $breed = $pool->petBreeds[$randomizer->getInt(0, count($pool->petBreeds) - 1)];

        return "{$name}, {$breed}";
    }
}
