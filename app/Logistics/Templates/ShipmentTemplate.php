<?php

namespace App\Logistics\Templates;

readonly class ShipmentTemplate
{
    /**
     * @param  array<string, string>  $sizeLabels
     * @param  array{subject: string, body: string}  $requestMail
     * @param  array<string, array{met: string, missed: string}>  $outcomeFeedback
     * @param  array{subject: string, intro: string, outro: string}  $feedbackMail
     * @param  array{subject: string, body: string}  $reminderMail
     * @param  array{subject: string, body: string}  $complaintMail
     */
    public function __construct(
        public string $slug,
        public string $responsibility,
        public string $dueFormat,
        public array $sizeLabels,
        public array $requestMail,
        public array $outcomeFeedback,
        public array $feedbackMail,
        public array $reminderMail,
        public array $complaintMail,
    ) {}
}
