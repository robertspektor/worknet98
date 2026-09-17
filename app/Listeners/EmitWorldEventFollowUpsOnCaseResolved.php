<?php

namespace App\Listeners;

use App\Cases\Events\CaseResolved;
use App\World\Events\FollowUpEmitter;

class EmitWorldEventFollowUpsOnCaseResolved
{
    public function __construct(private readonly FollowUpEmitter $emitter) {}

    public function handle(CaseResolved $event): void
    {
        $this->emitter->emitFor($event->workCase);
    }
}
