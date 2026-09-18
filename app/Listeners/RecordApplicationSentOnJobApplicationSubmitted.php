<?php

namespace App\Listeners;

use App\Careers\Events\JobApplicationSubmitted;
use App\Metrics\FunnelStep;
use App\Metrics\PlayerEvents;

class RecordApplicationSentOnJobApplicationSubmitted
{
    public function __construct(private readonly PlayerEvents $events) {}

    public function handle(JobApplicationSubmitted $event): void
    {
        $this->events->record($event->application->user, FunnelStep::ApplicationSent);
    }
}
