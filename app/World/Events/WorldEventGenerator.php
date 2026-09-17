<?php

namespace App\World\Events;

use App\Game\GameClock;
use App\Models\City;
use App\Models\WorldEvent;

class WorldEventGenerator
{
    public function __construct(
        private readonly GameClock $clock,
        private readonly EventCatalog $catalog,
        private readonly WorldEventPlanner $planner,
        private readonly ServiceProviders $providers,
        private readonly SubjectPicker $subjects,
        private readonly EventOccurrence $occurrence,
    ) {}

    public function generateDue(): int
    {
        return City::query()->orderBy('id')->get()->sum($this->generateFor(...));
    }

    private function generateFor(City $city): int
    {
        $now = $this->clock->now();
        $due = array_filter(
            $this->planner->plan($city, $this->catalog->all(), $now->startOfDay()),
            fn (PlannedEvent $planned): bool => $planned->occursAt->lte($now) && ! WorldEvent::query()->where('key', $planned->key)->exists(),
        );

        return count(array_filter($due, fn (PlannedEvent $planned): bool => $this->occur($city, $planned)));
    }

    private function occur(City $city, PlannedEvent $planned): bool
    {
        $service = $planned->definition->service;
        $provider = $service === null ? null : $this->providers->branchFor($city, $service);
        $person = $this->subjects->pick($city, $provider, $planned->key);

        return $person !== null
            && $this->occurrence->occur($city, $planned->key, $planned->definition, $person, $this->clock->toReal($planned->occursAt)) !== null;
    }
}
