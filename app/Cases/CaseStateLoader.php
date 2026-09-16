<?php

namespace App\Cases;

use App\Mailbox\EmailFolder;
use App\Models\CalendarEntry;
use App\Models\WorkCase;
use App\Workplace\BookingWindow;

class CaseStateLoader
{
    public function __construct(private readonly BookingWindow $window) {}

    public function for(WorkCase $workCase): CaseState
    {
        $employment = $workCase->employment;

        return new CaseState(
            workCase: $workCase,
            appointments: $employment->appointments()->with('technician')->get(),
            sentEmails: $employment->emails()->where('folder', EmailFolder::Sent)->get(),
            calendarEntries: CalendarEntry::query()->where('user_id', $employment->user_id)->get(),
            customers: $employment->company->customers()->get()->keyBy('slug'),
            window: $this->window,
        );
    }
}
