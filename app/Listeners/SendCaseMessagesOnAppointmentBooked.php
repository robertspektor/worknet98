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
        $bookedBy = $event->appointment->booked_by_employment_id;

        if ($bookedBy !== null) {
            $this->messenger->sendForOpenCases($bookedBy, MessageTrigger::AppointmentBooked);
        }
    }
}
