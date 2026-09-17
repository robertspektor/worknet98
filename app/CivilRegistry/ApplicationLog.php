<?php

namespace App\CivilRegistry;

use App\Models\Branch;
use App\Models\CivilApplication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ApplicationLog
{
    private const DECIDED_VISIBLE_REAL_HOURS = 24;

    /**
     * @return Collection<int, CivilApplication>
     */
    public function recentOf(Branch $office): Collection
    {
        return CivilApplication::query()
            ->whereBelongsTo($office)
            ->where(fn (Builder $query) => $query
                ->whereNull('decision')
                ->orWhere('decided_at', '>=', now()->subHours(self::DECIDED_VISIBLE_REAL_HOURS)))
            ->with(['applicant', 'partner', 'workCase', 'decidedBy.position', 'decidedByPosition.person'])
            ->orderByRaw('decision is not null')
            ->oldest('id')
            ->get();
    }
}
