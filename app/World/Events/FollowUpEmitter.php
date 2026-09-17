<?php

namespace App\World\Events;

use App\Models\WorkCase;
use App\Models\WorldEvent;
use Carbon\CarbonImmutable;

class FollowUpEmitter
{
    public function __construct(
        private readonly EventCatalog $catalog,
        private readonly RepairTiming $timing,
        private readonly EventOccurrence $occurrence,
    ) {}

    public function emitFor(WorkCase $workCase): void
    {
        $event = $workCase->worldEvent;

        if ($event === null) {
            return;
        }

        foreach ($this->catalog->find($event->type)->followUps as $followUp) {
            if ($this->isTriggered($followUp->when, $workCase, $event)) {
                $this->occurrence->occur($event->city, "{$event->key}|{$followUp->type}", $this->catalog->find($followUp->type), $event->person, CarbonImmutable::now(), $event);
            }
        }
    }

    private function isTriggered(FollowUpTrigger $trigger, WorkCase $workCase, WorldEvent $event): bool
    {
        return match ($trigger) {
            FollowUpTrigger::RepairLate => $this->timing->wasLate($workCase, $event),
        };
    }
}
