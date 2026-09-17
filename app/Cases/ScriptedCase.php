<?php

namespace App\Cases;

readonly class ScriptedCase
{
    /**
     * @param  array{subject: string, body: string}|null  $briefingMail
     */
    public function __construct(
        public int $shift,
        public CaseDefinition $definition,
        public ?array $briefingMail = null,
    ) {}
}
