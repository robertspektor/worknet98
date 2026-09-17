<?php

namespace App\Careers;

use App\Content\CompanyContentFile;
use App\Models\Company;

class OnboardingCatalog
{
    private const CONTENT_DIRECTORY = 'onboarding';

    /**
     * @return array{subject: string, body: string}|null
     */
    public function briefingFor(Company $company): ?array
    {
        /** @var array{briefing_mail?: array{subject: string, body: string}} $content */
        $content = CompanyContentFile::data($company, self::CONTENT_DIRECTORY);

        return $content['briefing_mail'] ?? null;
    }
}
