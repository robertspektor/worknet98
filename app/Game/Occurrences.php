<?php

namespace App\Game;

use Random\Randomizer;

class Occurrences
{
    public function count(Randomizer $randomizer, float $dailyRate): int
    {
        $threshold = exp(-$dailyRate);
        $product = $randomizer->nextFloat();
        $count = 0;

        while ($product > $threshold) {
            $count++;
            $product *= $randomizer->nextFloat();
        }

        return $count;
    }
}
