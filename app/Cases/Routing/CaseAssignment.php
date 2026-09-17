<?php

namespace App\Cases\Routing;

use App\Cases\WorkCaseStatus;
use App\Models\Branch;
use App\Models\WorkCase;
use App\Work\OpenShift;
use Illuminate\Support\Facades\DB;

class CaseAssignment
{
    public function __construct(
        private readonly CaseRouter $router,
        private readonly OpenShift $openShift,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function assign(Branch $branch, string $responsibility, array $attributes): WorkCase
    {
        return DB::transaction(function () use ($branch, $responsibility, $attributes): WorkCase {
            Branch::query()->whereKey($branch->id)->lockForUpdate()->first();
            $position = $this->router->assigneeFor($branch, $responsibility);
            $employment = $position->holder;

            return WorkCase::create([
                ...$attributes,
                'branch_id' => $branch->id,
                'position_id' => $position->id,
                'employment_id' => $employment?->id,
                'status' => WorkCaseStatus::Open,
                'opened_at' => now(),
                'npc_due_at' => $employment === null ? now()->addSeconds($this->npcDelaySeconds()) : null,
                'seen_at' => $employment !== null && $this->openShift->of($employment) !== null ? now() : null,
            ]);
        });
    }

    private function npcDelaySeconds(): int
    {
        return (int) config('game.npc_case_delay_seconds');
    }
}
