<?php

namespace App\Policies;

use App\Models\CalendarEntry;
use App\Models\User;

class CalendarEntryPolicy
{
    public function delete(User $player, CalendarEntry $entry): bool
    {
        return $entry->user_id === $player->id;
    }
}
