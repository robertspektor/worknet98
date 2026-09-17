<?php

namespace App\Logistics\Supply;

use App\Game\Occurrences;
use App\Models\City;
use Carbon\CarbonImmutable;
use Random\Engine\Mt19937;
use Random\Randomizer;

class SupplyOrderPlanner
{
    private const FIRST_MINUTE = 8 * 60;

    private const LAST_MINUTE = 15 * 60;

    public function __construct(private readonly Occurrences $occurrences) {}

    /**
     * @param  list<SupplyRoute>  $routes
     * @return list<PlannedSupplyOrder>
     */
    public function plan(City $city, array $routes, CarbonImmutable $day): array
    {
        if (! $day->isWeekday()) {
            return [];
        }

        $seed = "supply|{$city->slug}|{$day->toDateString()}";
        $randomizer = new Randomizer(new Mt19937(crc32($seed)));
        $orders = [];

        foreach ($routes as $route) {
            $count = $this->occurrences->count($randomizer, $route->dailyRate);

            for ($number = 1; $number <= $count; $number++) {
                $orders[] = new PlannedSupplyOrder(
                    key: "{$seed}|{$route->slug}|{$number}",
                    route: $route,
                    placedAt: $day->addMinutes($randomizer->getInt(self::FIRST_MINUTE, self::LAST_MINUTE)),
                );
            }
        }

        return $orders;
    }
}
