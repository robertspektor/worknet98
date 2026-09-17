<?php

namespace App\World\Events;

use App\Game\Occurrences;
use App\Models\City;
use Carbon\CarbonImmutable;
use Random\Engine\Mt19937;
use Random\Randomizer;

class WorldEventPlanner
{
    private const FIRST_MINUTE = 8 * 60;

    private const LAST_MINUTE = 16 * 60;

    public function __construct(private readonly Occurrences $occurrences) {}

    /**
     * @param  list<EventDefinition>  $definitions
     * @return list<PlannedEvent>
     */
    public function plan(City $city, array $definitions, CarbonImmutable $day): array
    {
        if (! $day->isWeekday()) {
            return [];
        }

        $seed = "{$city->slug}|{$day->toDateString()}";
        $randomizer = new Randomizer(new Mt19937(crc32($seed)));
        $events = [];

        foreach ($definitions as $definition) {
            $count = $this->occurrences->count($randomizer, $definition->dailyRate);

            for ($number = 1; $number <= $count; $number++) {
                $events[] = new PlannedEvent(
                    key: "{$seed}|{$definition->type}|{$number}",
                    definition: $definition,
                    occursAt: $day->addMinutes($randomizer->getInt(self::FIRST_MINUTE, self::LAST_MINUTE)),
                );
            }
        }

        return $events;
    }
}
