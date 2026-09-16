<?php

namespace App\Listeners;

use App\Cases\CaseMessenger;
use App\Cases\MessageTrigger;
use App\Workplace\Events\AppointmentBooked;

class SendCaseMessagesOnAppointmentBooked
{
    public function __construct(private readonly CaseMessenger $messenger) {}

    public function handle(AppointmentBooked $event): void
    {
        $this->messenger->sendForOpenCases($event->appointment->employment_id, MessageTrigger::AppointmentBooked);
    }
}
