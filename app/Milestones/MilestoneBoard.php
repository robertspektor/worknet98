<?php

namespace App\Milestones;

use App\Models\PlayerMilestone;
use App\Models\User;

class MilestoneBoard
{
    public function __construct(private readonly MilestoneCatalog $catalog) {}

    /**
     * @return list<MilestoneProgress>
     */
    public function for(User $player): array
    {
        $achieved = PlayerMilestone::query()
            ->whereBelongsTo($player)
            ->pluck('achieved_at', 'milestone_key');

        return array_map(fn (Milestone $milestone): MilestoneProgress => new MilestoneProgress(
            key: $milestone->key,
            achievedAt: $achieved[$milestone->key] ?? null,
        ), $this->catalog->all());
    }
}
