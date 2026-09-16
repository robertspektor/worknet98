<?php

namespace App\Listeners;

use App\Cases\CaseMessenger;
use App\Cases\Events\CaseOpened;
use App\Cases\MessageTrigger;

class SendCaseMessagesOnCaseOpened
{
    public function __construct(private readonly CaseMessenger $messenger) {}

    public function handle(CaseOpened $event): void
    {
        $this->messenger->send($event->workCase, MessageTrigger::CaseOpened);
    }
}
