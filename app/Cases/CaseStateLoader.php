<?php

namespace App\Cases;

use App\Game\GameClock;
use App\Mailbox\EmailFolder;
use App\Models\CalendarEntry;
use App\Models\WorkCase;
use App\Workplace\BookingWindow;

class CaseStateLoader
{
    public function __construct(
        private readonly BookingWindow $window,
        private readonly GameClock $clock,
    ) {}

    public function for(WorkCase $workCase): CaseState
    {
        $employment = $workCase->playerEmployment();

        return new CaseState(
            openedOn: $this->clock->fromReal($workCase->seen_at ?? $workCase->opened_at),
            appointments: $employment->bookedAppointments()->with('technician.person')->get(),
            sentEmails: $employment->emails()->where('folder', EmailFolder::Sent)->get(),
            calendarEntries: CalendarEntry::query()->where('user_id', $employment->user_id)->get(),
            customers: $workCase->branch->customers()->with('person')->get()->keyBy('person.slug'),
            window: $this->window,
            shipment: $workCase->shipment?->load('driver'),
            civilApplication: $workCase->civilApplication,
        );
    }
}
