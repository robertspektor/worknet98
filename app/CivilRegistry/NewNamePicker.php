<?php

namespace App\CivilRegistry;

use App\Models\Person;
use App\World\Population\NamePool;
use Random\Randomizer;

class NewNamePicker
{
    public function pick(Person $person, Randomizer $randomizer): string
    {
        $pool = NamePool::forLocale($person->city->locale);
        $givenName = explode(' ', $person->name)[0];

        do {
            $lastName = $pool->lastNames[$randomizer->getInt(0, count($pool->lastNames) - 1)];
        } while ("{$givenName} {$lastName}" === $person->name);

        return "{$givenName} {$lastName}";
    }
}
