<?php

namespace App\Cases;

use App\Cases\Conditions\Condition;

readonly class CaseDefinition
{
    /**
     * @param  array{customer: string, subject: string, body: string}  $requestMail
     * @param  list<Condition>  $goals
     * @param  list<Outcome>  $outcomes
     * @param  array{subject: string, intro: string, outro: string}  $feedbackMail
     * @param  array{subject: string, body: string}  $reminderMail
     */
    public function __construct(
        public string $slug,
        public int $shift,
        public array $requestMail,
        public array $goals,
        public array $outcomes,
        public array $feedbackMail,
        public array $reminderMail,
    ) {}
}
