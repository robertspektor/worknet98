<?php

namespace App\Cases\Templates;

use App\Cases\CaseMails;
use App\Cases\Events\CaseOpened;
use App\Cases\Routing\CaseRouter;
use App\Cases\WorkCaseKind;
use App\Cases\WorkCaseStatus;
use App\Mailbox\Mailbox;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\WorkCase;
use App\Work\TodaysShift;
use Illuminate\Support\Facades\DB;

class TemplateCaseOpener
{
    public function __construct(
        private readonly CaseRouter $router,
        private readonly TemplateCaseBuilder $builder,
        private readonly CaseMails $mails,
        private readonly Mailbox $mailbox,
        private readonly TodaysShift $todaysShift,
    ) {}

    public function open(Branch $branch, CaseTemplate $template, Customer $customer, ?string $demandKey = null): WorkCase
    {
        $workCase = DB::transaction(fn (): WorkCase => $this->assign($branch, $template, $customer, $demandKey));
        $employment = $workCase->employment;

        if ($employment !== null) {
            $request = $this->mails->request($this->builder->build($template, $customer), $customer);
            $this->mailbox->deliver($employment->user, $request, $employment);

            CaseOpened::dispatch($workCase);
        }

        return $workCase;
    }

    private function assign(Branch $branch, CaseTemplate $template, Customer $customer, ?string $demandKey): WorkCase
    {
        Branch::query()->whereKey($branch->id)->lockForUpdate()->first();
        $position = $this->router->assigneeFor($branch, $template->responsibility);
        $employment = $position->holder;

        return WorkCase::create([
            'branch_id' => $branch->id,
            'position_id' => $position->id,
            'employment_id' => $employment?->id,
            'customer_id' => $customer->id,
            'kind' => WorkCaseKind::Template,
            'case_slug' => $template->slug,
            'demand_key' => $demandKey,
            'status' => WorkCaseStatus::Open,
            'opened_at' => now(),
            'npc_due_at' => $employment === null ? now()->addSeconds($this->npcDelaySeconds()) : null,
            'seen_at' => $employment !== null && $this->todaysShift->of($employment)?->isOnDuty() ? now() : null,
        ]);
    }

    private function npcDelaySeconds(): int
    {
        return (int) config('game.npc_case_delay_seconds');
    }
}
