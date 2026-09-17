<?php

namespace App\Citynet;

use App\Models\City;

readonly class CityOverview
{
    public function __construct(
        public City $city,
        public int $residents,
        public int $households,
        public int $employed,
        public int $unemployed,
        public int $averageIncome,
        public int $organizations,
        public int $openPositions,
    ) {}

    public function unemploymentRate(): float
    {
        $workforce = $this->employed + $this->unemployed;

        return $workforce === 0 ? 0.0 : round($this->unemployed / $workforce * 100, 1);
    }
}
