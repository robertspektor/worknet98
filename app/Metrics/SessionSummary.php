<?php

namespace App\Metrics;

use App\Models\PlayerSession;

class SessionSummary
{
    public function sinceDays(int $days): SessionLengths
    {
        $sessions = PlayerSession::query()->where('started_at', '>=', now()->subDays($days))->get();

        /** @var list<int> $minutes */
        $minutes = $sessions->map(fn (PlayerSession $session): int => $session->lengthInMinutes())->sort()->values()->all();

        return new SessionLengths(
            sessions: count($minutes),
            players: $sessions->pluck('user_id')->unique()->count(),
            medianMinutes: $minutes === [] ? 0 : $minutes[intdiv(count($minutes), 2)],
            longestMinutes: $minutes === [] ? 0 : max($minutes),
        );
    }
}
