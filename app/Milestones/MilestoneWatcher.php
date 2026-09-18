<?php

namespace App\Milestones;

use App\Mailbox\Mailbox;
use App\Models\PlayerMilestone;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class MilestoneWatcher
{
    public function __construct(
        private readonly MilestoneCatalog $catalog,
        private readonly MilestoneLetter $letter,
        private readonly Mailbox $mailbox,
    ) {}

    public function awardReached(): int
    {
        $milestones = $this->catalog->all();
        $awarded = 0;

        User::query()
            ->lazyById()
            ->each(function (User $player) use ($milestones, &$awarded): void {
                $awarded += $this->awardFor($player, $milestones);
            });

        return $awarded;
    }

    /**
     * @param  list<Milestone>  $milestones
     */
    private function awardFor(User $player, array $milestones): int
    {
        $achieved = PlayerMilestone::query()->whereBelongsTo($player)->pluck('milestone_key')->all();
        $awarded = 0;

        foreach ($milestones as $milestone) {
            if (! in_array($milestone->key, $achieved, true) && $this->award($player, $milestone)) {
                $awarded++;
            }
        }

        return $awarded;
    }

    private function award(User $player, Milestone $milestone): bool
    {
        if (! $milestone->condition->isMetBy($player)) {
            return false;
        }

        try {
            DB::transaction(function () use ($player, $milestone): void {
                PlayerMilestone::create([
                    'user_id' => $player->id,
                    'milestone_key' => $milestone->key,
                    'achieved_at' => now(),
                ]);

                $this->announce($player, $milestone);
            });
        } catch (UniqueConstraintViolationException) {
            return false;
        }

        return true;
    }

    private function announce(User $player, Milestone $milestone): void
    {
        $sender = $milestone->senderKey === null
            ? null
            : $this->catalog->senderFor($milestone->senderKey, $player->locale);

        if ($sender !== null) {
            $this->mailbox->deliver($player, $this->letter->compose($sender, $milestone->key, $player->locale));
        }
    }
}
