<?php

namespace App\Calendar;

use App\Models\CalendarEntry;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

class CalendarBook
{
    /**
     * @return Collection<int, CalendarEntry>
     */
    public function upcomingEntriesOf(User $player): Collection
    {
        return CalendarEntry::query()
            ->where('user_id', $player->id)
            ->whereDate('date', '>=', CarbonImmutable::today()->toDateString())
            ->orderBy('date')
            ->orderBy('time')
            ->get();
    }

    public function add(User $player, CarbonImmutable $date, string $time, string $title): CalendarEntry
    {
        return CalendarEntry::create([
            'user_id' => $player->id,
            'date' => $date->toDateString(),
            'time' => $time,
            'title' => $title,
        ]);
    }

    public function remove(CalendarEntry $entry): void
    {
        $entry->delete();
    }
}
