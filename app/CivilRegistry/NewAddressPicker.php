<?php

namespace App\CivilRegistry;

use App\Models\City;
use App\Models\Household;
use App\World\Population\NamePool;
use Random\Randomizer;

class NewAddressPicker
{
    private const MAX_HOUSE_NUMBER = 180;

    /**
     * @param  list<string>  $districts
     * @return array{district: string, street: string}
     */
    public function pick(City $city, array $districts, Randomizer $randomizer): array
    {
        $pool = NamePool::forLocale($city->locale);

        do {
            $district = $districts[$randomizer->getInt(0, count($districts) - 1)];
            $street = strtr($pool->streetFormat, [
                ':street' => $pool->streets[$randomizer->getInt(0, count($pool->streets) - 1)],
                ':number' => (string) $randomizer->getInt(1, self::MAX_HOUSE_NUMBER),
            ]);
        } while ($this->isTaken($city, $district, $street));

        return ['district' => $district, 'street' => $street];
    }

    private function isTaken(City $city, string $district, string $street): bool
    {
        return Household::query()->whereBelongsTo($city)->where('district', $district)->where('street', $street)->exists();
    }
}
