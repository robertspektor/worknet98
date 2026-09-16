<?php

namespace App\Cases\Conditions;

use App\Cases\CaseState;
use App\Mailbox\EmailAction;

readonly class EmailSent implements Condition
{
    public function __construct(
        private string $customer,
        private EmailAction $action,
    ) {}

    public function isMetBy(CaseState $state): bool
    {
        return $state->hasSentEmail($this->customer, $this->action);
    }
}
