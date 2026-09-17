<?php

namespace App\World\Events;

use Illuminate\Support\Facades\File;
use LogicException;

class EventCatalog
{
    /** @var array<string, EventDefinition>|null */
    private ?array $definitions = null;

    /**
     * @return list<EventDefinition>
     */
    public function all(): array
    {
        return array_values($this->definitions());
    }

    public function find(string $type): EventDefinition
    {
        return $this->definitions()[$type] ?? throw new LogicException("Unknown world event type [{$type}].");
    }

    /**
     * @return array<string, EventDefinition>
     */
    private function definitions(): array
    {
        return $this->definitions ??= $this->load();
    }

    /**
     * @return array<string, EventDefinition>
     */
    private function load(): array
    {
        /** @var list<array{type: string, daily_rate: float|int, service?: string, case_template?: string, follow_ups?: list<array{type: string, when: string}>}> $entries */
        $entries = File::json(database_path('content/world_events.json'), JSON_THROW_ON_ERROR);

        return collect($entries)
            ->mapWithKeys(fn (array $entry): array => [$entry['type'] => new EventDefinition(
                type: $entry['type'],
                dailyRate: (float) $entry['daily_rate'],
                service: $entry['service'] ?? null,
                caseTemplate: $entry['case_template'] ?? null,
                followUps: array_map(
                    fn (array $followUp): FollowUp => new FollowUp($followUp['type'], FollowUpTrigger::from($followUp['when'])),
                    $entry['follow_ups'] ?? [],
                ),
            )])
            ->all();
    }
}
