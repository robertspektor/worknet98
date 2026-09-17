<?php

namespace App\Cases\Npc;

use App\Cases\Templates\CaseTemplateCatalog;
use App\Game\ActionRefused;
use App\Models\WorkCase;
use App\Workplace\AppointmentBooker;
use App\Workplace\FreeSlotFinder;
use Illuminate\Support\Facades\DB;

class NpcCaseWorker
{
    public function __construct(
        private readonly CaseTemplateCatalog $templates,
        private readonly FreeSlotFinder $slots,
        private readonly AppointmentBooker $booker,
    ) {}

    public function workDue(): int
    {
        $worked = 0;

        WorkCase::query()
            ->dueForNpc()
            ->with(['branch.company', 'customer.branch', 'position'])
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
        $template = $this->templates->find($workCase->branch->company, $workCase->case_slug);
        $request = $template === null ? null : $this->slots->earliestFor($workCase->customer, $template->skill);

        if ($request === null) {
            return false;
        }

        try {
            DB::transaction(function () use ($workCase, $request): void {
                $this->booker->bookForNpc($request, $workCase->position);
                $workCase->update(['npc_due_at' => null]);
            });
        } catch (ActionRefused) {
            return false;
        }

        return true;
    }
}
