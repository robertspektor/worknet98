<?php

namespace App\Logistics;

use App\Cases\WorkCaseKind;
use App\Game\ActionRefused;
use App\Models\WorkCase;
use Illuminate\Support\Facades\DB;

class NpcDispatcher
{
    public function __construct(
        private readonly FreeTourFinder $tours,
        private readonly TourPlanner $planner,
    ) {}

    public function workDue(): int
    {
        $worked = 0;

        WorkCase::query()
            ->dueForNpc()
            ->where('kind', WorkCaseKind::Shipment)
            ->with(['shipment.branch', 'position'])
            ->lazyById()
            ->each(function (WorkCase $workCase) use (&$worked): void {
                if ($this->work($workCase)) {
                    $worked++;
                }
            });

        return $worked;
    }

    private function work(WorkCase $workCase): bool
    {
        $shipment = $workCase->shipment;

        if ($shipment === null) {
            return false;
        }

        if ($shipment->isPlanned()) {
            $workCase->update(['npc_due_at' => null]);

            return true;
        }

        $plan = $this->tours->bestFor($shipment);

        if ($plan === null) {
            return false;
        }

        try {
            DB::transaction(function () use ($workCase, $plan): void {
                $this->planner->planForNpc($plan, $workCase->position);
                $workCase->update(['npc_due_at' => null]);
            });
        } catch (ActionRefused) {
            return false;
        }

        return true;
    }
}
