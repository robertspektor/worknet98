<?php

namespace App\Metrics;

use App\Milestones\Milestone;
use App\Milestones\MilestoneCatalog;
use App\Models\PlayerEvent;
use App\Models\PlayerMilestone;
use App\Models\User;

class Funnel
{
    public function __construct(private readonly MilestoneCatalog $milestones) {}

    /**
     * @return list<FunnelRow>
     */
    public function steps(): array
    {
        $counts = ['registered' => User::query()->count(), ...$this->stepCounts(), ...$this->milestoneCounts()];
        $rows = [];
        $previous = null;

        foreach ($counts as $label => $players) {
            $rows[] = new FunnelRow($label, $players, $previous === null ? 0 : $previous - $players);
            $previous = $players;
        }

        return $rows;
    }

    /**
     * @return array<string, int>
     */
    private function stepCounts(): array
    {
        $recorded = PlayerEvent::query()
            ->selectRaw('name, count(distinct user_id) as players')
            ->groupBy('name')
            ->pluck('players', 'name');

        return collect(FunnelStep::cases())
            ->mapWithKeys(fn (FunnelStep $step): array => [$step->value => (int) $recorded->get($step->value, 0)])
            ->all();
    }

    /**
     * @return array<string, int>
     */
    private function milestoneCounts(): array
    {
        $reached = PlayerMilestone::query()
            ->selectRaw('milestone_key, count(distinct user_id) as players')
            ->groupBy('milestone_key')
            ->pluck('players', 'milestone_key');

        return collect($this->milestones->all())
            ->mapWithKeys(fn (Milestone $milestone): array => [$milestone->key => (int) $reached->get($milestone->key, 0)])
            ->all();
    }
}
