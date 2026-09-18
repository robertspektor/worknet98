<?php

namespace App\Milestones;

use App\Milestones\Conditions\MilestoneConditionFactory;
use Illuminate\Support\Facades\File;

class MilestoneCatalog
{
    public function __construct(private readonly MilestoneConditionFactory $conditions) {}

    /**
     * @return list<Milestone>
     */
    public function all(): array
    {
        return array_map(fn (array $milestone): Milestone => new Milestone(
            key: $milestone['key'],
            condition: $this->conditions->make($milestone['condition']),
            senderKey: $milestone['sender'] ?? null,
        ), $this->content()['milestones']);
    }

    public function senderFor(string $senderKey, string $locale): ?MilestoneSender
    {
        $sender = $this->content()['senders'][$locale][$senderKey] ?? null;

        return $sender === null ? null : new MilestoneSender($sender['name'], $sender['address']);
    }

    /**
     * @return array{
     *     senders: array<string, array<string, array{name: string, address: string}>>,
     *     milestones: list<array{key: string, sender?: string, condition: array<string, mixed>}>
     * }
     */
    private function content(): array
    {
        /** @var array{
         *     senders: array<string, array<string, array{name: string, address: string}>>,
         *     milestones: list<array{key: string, sender?: string, condition: array<string, mixed>}>
         * } */
        return File::json(database_path('content/milestones.json'), JSON_THROW_ON_ERROR);
    }
}
