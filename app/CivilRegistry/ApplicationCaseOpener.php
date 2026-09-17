<?php

namespace App\CivilRegistry;

use App\Cases\CaseMails;
use App\Cases\Events\CaseOpened;
use App\Cases\Routing\CaseAssignment;
use App\Cases\WorkCaseKind;
use App\CivilRegistry\Templates\ApplicationCaseBuilder;
use App\CivilRegistry\Templates\ApplicationTemplate;
use App\Mailbox\Mailbox;
use App\Models\CivilApplication;
use App\Models\WorkCase;
use App\Models\WorldEvent;
use App\Workplace\CustomerIntake;

class ApplicationCaseOpener
{
    public function __construct(
        private readonly CustomerIntake $intake,
        private readonly CaseAssignment $assignment,
        private readonly ApplicationCaseBuilder $builder,
        private readonly CaseMails $mails,
        private readonly Mailbox $mailbox,
    ) {}

    public function open(CivilApplication $application, ApplicationTemplate $template, WorldEvent $event): WorkCase
    {
        $applicant = $this->intake->customerFor($application->branch, $application->applicant);
        $workCase = $this->assignment->assign($application->branch, $template->responsibility, [
            'customer_id' => $applicant->id,
            'kind' => WorkCaseKind::Application,
            'case_slug' => $template->slug,
            'civil_application_id' => $application->id,
            'world_event_id' => $event->id,
        ]);
        $employment = $workCase->employment;

        if ($employment !== null) {
            $request = $this->mails->request($this->builder->build($template, $application), $applicant);
            $this->mailbox->deliver($employment->user, $request, $employment);

            CaseOpened::dispatch($workCase);
        }

        return $workCase;
    }
}
