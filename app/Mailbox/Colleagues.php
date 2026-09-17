<?php

namespace App\Mailbox;

use App\Models\Employment;
use App\Models\Position;
use Illuminate\Database\Eloquent\Collection;

class Colleagues
{
    /**
     * @return Collection<int, Position>
     */
    public function of(Employment $employment): Collection
    {
        return Position::query()
            ->where('branch_id', $employment->position->branch_id)
            ->whereKeyNot($employment->position_id)
            ->with('person')
            ->get()
            ->sortBy('person.name')
            ->values();
    }
}
