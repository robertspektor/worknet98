<?php

namespace App\Cases\Npc;

use App\Cases\Templates\CaseTemplateCatalog;
use App\Cases\WorkCaseStatus;
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
            ->with(['branch.company', 'customer.branch'])
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
        $request = $template && $workCase->customer ? $this->slots->earliestFor($workCase->customer, $template->skill) : null;

        if ($request === null) {
            return false;
        }

        try {
            DB::transaction(function () use ($workCase, $request): void {
                $this->booker->bookForOffice($request);
                $workCase->update(['status' => WorkCaseStatus::Resolved, 'resolved_at' => now()]);
            });
        } catch (ActionRefused) {
            return false;
        }

        return true;
    }
}
