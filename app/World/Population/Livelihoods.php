<?php

namespace App\World\Population;

use Random\Randomizer;

class Livelihoods
{
    private const INCOME_STEP = 10;

    public function draw(Randomizer $randomizer, NamePool $pool): Livelihood
    {
        $occupation = $this->occupation($randomizer, $pool);
        [$from, $to] = $pool->incomeRanges[$occupation->value];

        return new Livelihood(
            occupation: $occupation,
            profession: $occupation->needsProfession() ? $pool->professions[$randomizer->getInt(0, count($pool->professions) - 1)] : null,
            monthlyIncome: $randomizer->getInt((int) ($from / self::INCOME_STEP), (int) ($to / self::INCOME_STEP)) * self::INCOME_STEP,
        );
    }

    private function occupation(Randomizer $randomizer, NamePool $pool): Occupation
    {
        $roll = $randomizer->getInt(1, array_sum($pool->occupationWeights));

        foreach ($pool->occupationWeights as $occupation => $weight) {
            $roll -= $weight;

            if ($roll <= 0) {
                return Occupation::from($occupation);
            }
        }

        return Occupation::Employed;
    }
}
