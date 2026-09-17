<?php

namespace App\Citynet;

use App\Models\WorldEvent;
use Illuminate\Database\Eloquent\Collection;

class EventChronicle
{
    private const LIMIT = 40;

    /**
     * @return Collection<int, WorldEvent>
     */
    public function latest(): Collection
    {
        return WorldEvent::query()
            ->with(['city', 'person', 'parent', 'workCase.branch.company'])
            ->latest('occurred_at')
            ->limit(self::LIMIT)
            ->get();
    }
}
