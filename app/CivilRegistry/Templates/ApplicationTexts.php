<?php

namespace App\CivilRegistry\Templates;

use App\Models\CivilApplication;

readonly class ApplicationTexts
{
    public function __construct(
        private ApplicationTemplate $template,
        private CivilApplication $application,
    ) {}

    public function fill(string $text): string
    {
        $application = $this->application;

        return strtr($text, [
            ':applicant' => $application->applicant->name,
            ':claimed_address' => "{$application->claimed_street}, {$application->claimed_district}",
            ':new_address' => "{$application->new_street}, {$application->new_district}",
            ':partner_address' => "{$application->claimed_partner_street}, {$application->claimed_partner_district}",
            ':partner' => $application->partner->name ?? '',
            ':detail' => $application->detail ?? '',
            ':moved_on' => $application->moved_on->isoFormat($this->template->dateFormat),
        ]);
    }
}
