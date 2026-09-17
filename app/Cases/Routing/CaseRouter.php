<?php

namespace App\Cases\Routing;

use App\Models\Branch;
use App\Models\Position;
use LogicException;

class CaseRouter
{
    public function assigneeFor(Branch $branch, string $responsibility): Position
    {
        return Position::query()
            ->whereBelongsTo($branch)
            ->responsibleFor($responsibility)
            ->withCount(['workCases' => fn ($query) => $query->open()])
            ->orderBy('work_cases_count')
            ->orderBy('id')
            ->first()
            ?? throw new LogicException("Nobody at branch [{$branch->slug}] is responsible for [{$responsibility}].");
    }
}
