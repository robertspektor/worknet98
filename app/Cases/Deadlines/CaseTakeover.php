<?php

namespace App\Cases\Deadlines;

use App\Cases\Metric;
use App\Cases\MetricBook;
use App\Cases\Routing\CaseRouter;
use App\Cases\Templates\CaseTemplateCatalog;
use App\Cases\WorkCaseKind;
use App\Cases\WorkCaseStatus;
use App\Mailbox\Mailbox;
use App\Models\Position;
use App\Models\WorkCase;
use Illuminate\Support\Facades\DB;

class CaseTakeover
{
    public function __construct(
        private readonly CaseTemplateCatalog $templates,
        private readonly CaseRouter $router,
        private readonly MetricBook $metrics,
        private readonly TakeoverLetter $letter,
        private readonly Mailbox $mailbox,
    ) {}

    public function takeOver(WorkCase $workCase): void
    {
        $employment = $workCase->playerEmployment();

        DB::transaction(function () use ($workCase, $employment): void {
            $colleague = $this->colleagueFor($workCase);
            $draft = $colleague === null ? $this->letter->lost($workCase, $employment) : $this->letter->handedOver($workCase, $employment, $colleague);

            $colleague === null ? $this->lose($workCase) : $this->handOver($workCase, $colleague);
            $this->metrics->apply($employment, [Metric::Reliability->value => -1]);
            $this->mailbox->deliver($employment->user, $draft, $employment);
        });
    }

    private function colleagueFor(WorkCase $workCase): ?Position
    {
        $template = $workCase->kind === WorkCaseKind::Template
            ? $this->templates->find($workCase->branch->company, $workCase->case_slug)
            : null;

        return $template === null ? null : $this->router->npcAssigneeFor($workCase->branch, $template->responsibility);
    }

    private function handOver(WorkCase $workCase, Position $colleague): void
    {
        $workCase->update([
            'position_id' => $colleague->id,
            'employment_id' => null,
            'npc_due_at' => now()->addSeconds((int) config('game.npc_case_delay_seconds')),
        ]);
    }

    private function lose(WorkCase $workCase): void
    {
        $workCase->update(['status' => WorkCaseStatus::Lost, 'resolved_at' => now()]);
    }
}
