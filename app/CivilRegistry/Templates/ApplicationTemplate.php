<?php

namespace App\CivilRegistry\Templates;

use App\CivilRegistry\ApplicationKind;

readonly class ApplicationTemplate
{
    /**
     * @param  array{subject: string, body: string}  $requestMail
     * @param  array{approved_valid: string, approved_invalid: string, rejected_valid: string, rejected_invalid: string}  $outcomeFeedback
     * @param  array{subject: string, intro: string, outro: string}  $feedbackMail
     * @param  array{subject: string, body: string}  $reminderMail
     */
    public function __construct(
        public string $slug,
        public ApplicationKind $kind,
        public string $responsibility,
        public string $dateFormat,
        public array $requestMail,
        public array $outcomeFeedback,
        public array $feedbackMail,
        public array $reminderMail,
    ) {}
}
