<?php

namespace App\Cases\Templates;

use App\Cases\CaseMails;
use App\Cases\Events\CaseOpened;
use App\Cases\Routing\CaseAssignment;
use App\Cases\WorkCaseKind;
use App\Mailbox\Mailbox;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Position;
use App\Models\WorkCase;
use App\Models\WorldEvent;

class TemplateCaseOpener
{
    public function __construct(
        private readonly CaseAssignment $assignment,
        private readonly TemplateCaseBuilder $builder,
        private readonly CaseMails $mails,
        private readonly Mailbox $mailbox,
    ) {}

    public function open(Branch $branch, CaseTemplate $template, Customer $customer, ?WorldEvent $worldEvent = null, ?Position $assignee = null): WorkCase
    {
        $workCase = $this->assignment->assign($branch, $template->responsibility, [
            'customer_id' => $customer->id,
            'kind' => WorkCaseKind::Template,
            'case_slug' => $template->slug,
            'world_event_id' => $worldEvent?->id,
        ], $assignee);
        $employment = $workCase->employment;

        if ($employment !== null) {
            $request = $this->mails->request($this->builder->build($template, $customer), $customer);
            $this->mailbox->deliver($employment->user, $request, $employment);

            CaseOpened::dispatch($workCase);
        }

        return $workCase;
    }
}
