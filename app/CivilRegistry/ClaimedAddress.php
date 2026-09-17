<?php

namespace App\CivilRegistry;

use App\Models\Household;
use Random\Randomizer;

class ClaimedAddress
{
    /**
     * @return array{district: string, street: string}
     */
    public function of(Household $household, bool $withMistake, Randomizer $randomizer): array
    {
        return [
            'district' => $household->district,
            'street' => $withMistake ? $this->wrongHouseNumber($household->street, $randomizer) : $household->street,
        ];
    }

    private function wrongHouseNumber(string $street, Randomizer $randomizer): string
    {
        return preg_replace_callback(
            '/\d+/',
            fn (array $number): string => (string) ((int) $number[0] + $randomizer->getInt(1, 9)),
            $street,
            1,
        ) ?? $street;
    }
}
