<?php

namespace App\Cases\Templates;

readonly class CaseTemplate
{
    /**
     * @param  array{subject: string, body: string}  $requestMail
     * @param  array<string, string>  $availabilityNotes
     * @param  array<string, array{met: string, missed: string}>  $outcomeFeedback
     * @param  array{subject: string, intro: string, outro: string}  $feedbackMail
     * @param  array{subject: string, body: string}  $reminderMail
     */
    public function __construct(
        public string $slug,
        public string $responsibility,
        public string $skill,
        public int $urgentWithinWorkDays,
        public array $requestMail,
        public array $availabilityNotes,
        public array $outcomeFeedback,
        public array $feedbackMail,
        public array $reminderMail,
    ) {}
}
