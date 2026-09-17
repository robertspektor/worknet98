<?php

namespace App\CivilRegistry;

use App\Models\Person;
use Random\Randomizer;

class PartnerPicker
{
    public function for(Person $applicant, Randomizer $randomizer): ?Person
    {
        $candidates = Person::query()
            ->where('city_id', $applicant->city_id)
            ->where('household_id', '!=', $applicant->household_id)
            ->whereDoesntHave('positions');
        $count = $candidates->count();

        return $count === 0 ? null : $candidates->orderBy('id')->offset($randomizer->getInt(0, $count - 1))->first();
    }
}
