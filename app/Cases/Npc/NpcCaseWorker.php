<?php

namespace App\Cases\Npc;

use App\Cases\Templates\CaseTemplateCatalog;
use App\Cases\WorkCaseKind;
use App\Game\ActionRefused;
use App\Logistics\PartLeadTime;
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
        private readonly PartLeadTime $leadTime,
    ) {}

    public function workDue(): int
    {
        $worked = 0;

        WorkCase::query()
            ->dueForNpc()
            ->where('kind', WorkCaseKind::Template)
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
        $request = $template === null ? null : $this->slots->earliestFor(
            $workCase->customer,
            $template->skill,
            $this->leadTime->earliestRepairStart($workCase, $template),
        );

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
