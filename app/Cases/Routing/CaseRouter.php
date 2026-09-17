<?php

namespace App\Cases\Routing;

use App\Models\Branch;
use App\Models\Position;
use Illuminate\Database\Eloquent\Builder;
use LogicException;

class CaseRouter
{
    public function assigneeFor(Branch $branch, string $responsibility): Position
    {
        return $this->leastBusy($branch, $responsibility)->first()
            ?? throw new LogicException("Nobody at branch [{$branch->slug}] is responsible for [{$responsibility}].");
    }

    public function npcAssigneeFor(Branch $branch, string $responsibility): ?Position
    {
        return $this->leastBusy($branch, $responsibility)->vacant()->first();
    }

    /**
     * @return Builder<Position>
     */
    private function leastBusy(Branch $branch, string $responsibility): Builder
    {
        return Position::query()
            ->whereBelongsTo($branch)
            ->responsibleFor($responsibility)
            ->withCount(['workCases' => fn ($query) => $query->open(), 'holder'])
            ->orderBy('work_cases_count')
            ->orderByDesc('holder_count')
            ->orderBy('id');
    }
}
