<?php

namespace App\Cases;

use App\Cases\Conditions\Condition;
use App\Messenger\CannedReply;

readonly class CaseMessage
{
    /**
     * @param  list<CannedReply>  $replies
     */
    public function __construct(
        public string $slug,
        public MessageTrigger $trigger,
        public int $delaySeconds,
        public string $sender,
        public ?Condition $when,
        public string $body,
        public array $replies,
    ) {}

    public function isDueIn(CaseState $state): bool
    {
        return $this->when === null || $this->when->isMetBy($state);
    }
}
