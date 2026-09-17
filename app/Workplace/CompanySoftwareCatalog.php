<?php

namespace App\Workplace;

use App\Content\CompanyContentFile;
use App\Models\Company;

class CompanySoftwareCatalog
{
    private const CONTENT_DIRECTORY = 'company_software';

    /**
     * @return array<string, string>
     */
    public function appNamesFor(Company $company): array
    {
        /** @var array<string, string> */
        return CompanyContentFile::data($company, self::CONTENT_DIRECTORY)['app_names'] ?? [];
    }
}
