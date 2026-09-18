<?php

namespace App\Metrics;

use App\Models\PlayerSession;
use App\Models\User;

class SessionRecorder
{
    private const GAP_MINUTES = 30;

    private const REFRESH_MINUTES = 1;

    public function touch(User $player): void
    {
        $session = $this->runningFor($player);

        if ($session === null) {
            PlayerSession::create(['user_id' => $player->id, 'started_at' => now(), 'last_seen_at' => now()]);

            return;
        }

        if ($session->last_seen_at->lte(now()->subMinutes(self::REFRESH_MINUTES))) {
            $session->update(['last_seen_at' => now()]);
        }
    }

    private function runningFor(User $player): ?PlayerSession
    {
        return PlayerSession::query()
            ->whereBelongsTo($player)
            ->where('last_seen_at', '>', now()->subMinutes(self::GAP_MINUTES))
            ->latest('last_seen_at')
            ->first();
    }
}
