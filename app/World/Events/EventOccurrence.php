<?php

namespace App\World\Events;

use App\Models\City;
use App\Models\Person;
use App\Models\WorldEvent;
use Carbon\CarbonImmutable;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class EventOccurrence
{
    public function __construct(private readonly WorldEventHandler $handler) {}

    public function occur(City $city, string $key, EventDefinition $definition, Person $person, CarbonImmutable $occurredAt, ?WorldEvent $parent = null): ?WorldEvent
    {
        if (WorldEvent::query()->where('key', $key)->exists()) {
            return null;
        }

        try {
            return DB::transaction(function () use ($city, $key, $definition, $person, $occurredAt, $parent): WorldEvent {
                $event = WorldEvent::create([
                    'city_id' => $city->id,
                    'parent_id' => $parent?->id,
                    'person_id' => $person->id,
                    'key' => $key,
                    'type' => $definition->type,
                    'occurred_at' => $occurredAt,
                ]);

                $this->handler->handle($event);

                return $event;
            });
        } catch (UniqueConstraintViolationException) {
            return null;
        }
    }
}
