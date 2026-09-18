<?php

namespace App\Work;

use App\Models\Employment;
use App\Models\Shift;
use Carbon\CarbonImmutable;

class Presence
{
    public function lastActiveAt(Employment $employment): ?CarbonImmutable
    {
        $lastActive = Shift::query()->where('employment_id', $employment->id)->max('last_active_at');

        return $lastActive === null ? null : CarbonImmutable::parse($lastActive);
    }

    public function lastActiveBefore(Shift $shift): ?CarbonImmutable
    {
        $lastActive = Shift::query()
            ->where('employment_id', $shift->employment_id)
            ->where('id', '<', $shift->id)
            ->max('last_active_at');

        return $lastActive === null ? null : CarbonImmutable::parse($lastActive);
    }

    public function isAway(Employment $employment): bool
    {
        $lastActiveAt = $this->lastActiveAt($employment);

        return $lastActiveAt === null || $lastActiveAt->lt(now()->subHours($this->awayAfterHours()));
    }

    public function awayAfterHours(): int
    {
        return (int) config('game.absence_after_hours');
    }
}
