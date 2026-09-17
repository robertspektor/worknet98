<?php

namespace App\World\Events;

use App\Cases\Templates\CaseTemplateCatalog;
use App\Game\GameClock;
use App\Models\Appointment;
use App\Models\WorkCase;
use App\Models\WorldEvent;
use App\Workplace\BookingWindow;

class RepairTiming
{
    public function __construct(
        private readonly CaseTemplateCatalog $templates,
        private readonly BookingWindow $window,
        private readonly GameClock $clock,
    ) {}

    public function wasLate(WorkCase $workCase, WorldEvent $event): bool
    {
        $template = $this->templates->find($workCase->branch->company, $workCase->case_slug);
        $appointment = Appointment::query()
            ->where('customer_id', $workCase->customer_id)
            ->whereNotNull('executed_at')
            ->notFailed()
            ->latest('executed_at')
            ->first();

        return $template !== null
            && $appointment !== null
            && $this->window->workDaysBetween($this->clock->fromReal($event->occurred_at), $appointment->date) > $template->urgentWithinWorkDays;
    }
}
