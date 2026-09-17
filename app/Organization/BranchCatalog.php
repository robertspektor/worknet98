<?php

namespace App\Organization;

use App\Content\CompanyContentFile;
use App\Models\Company;

class BranchCatalog
{
    private const CONTENT_DIRECTORY = 'branches';

    /**
     * @return list<array<string, mixed>>
     */
    public function branchesOf(Company $company): array
    {
        return CompanyContentFile::entries($company, self::CONTENT_DIRECTORY);
    }

    /**
     * @return list<string>
     */
    public function companySlugs(): array
    {
        return CompanyContentFile::companySlugs(self::CONTENT_DIRECTORY);
    }
}
